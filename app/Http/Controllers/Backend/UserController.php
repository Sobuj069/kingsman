<?php

namespace App\Http\Controllers\Backend;

use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        $users = User::where('status',1)->where('id','!=','2')->with('role')->orderBy('id', 'desc')->paginate(20);
        return view('backend.pages.user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('id','!=','2')->get();
        $branchs = Branch::all();
        return view('backend.pages.user.create', compact('roles','branchs'));
    }

    public function store(Request $request)
    {
        if ($request->password != $request->password_confirmation) {
            session()->flash('error', __('Password and confirm password does not match'));
            return back();
        }
        $user = new User();
        $user->role_id = $request->role_id;
        $user->name = $request->name;
        $user->branch_id = $request->branch_id;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->status = $request->status;
        $user->fake_sale_percentage = $request->fake_sale_percentage;
        $user->password = Hash::make($request->password_confirmation);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '-' . rand(0, 9999999) . '-' . $user->slug . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/users');
            $image->move($destinationPath, $name);
            $user->image = $name;
        }
        $user->save();
        session()->flash('success', __('User created successfully'));
        logActivity('Create User', "User '{$user->name}' ({$user->email}) created", $user);
        return Redirect()->route('user.index');
    }

    public function show($id)
    {
        $user = User::with('role')->find($id);
        return view('backend.pages.user.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::where('id','!=','2')->get();
        $branchs = Branch::all();
        return view('backend.pages.user.edit', compact('user', 'roles','branchs'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->role_id = $request->role_id;
        $user->name = $request->name;
        $user->branch_id = $request->branch_id;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->status = $request->status;
        $user->fake_sale_percentage = $request->fake_sale_percentage;

        if ($request->hasFile('image')) {
            if ($user->image != 'profile.svg') {
                if (file_exists(public_path('uploads/users' . $user->image))) {
                    @unlink(public_path('uploads/users' . $user->image));
                }
            }
            $image = $request->file('image');
            $name = time() . '-' . rand(0, 9999999) . '-' . $user->slug . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/users');
            $image->move($destinationPath, $name);
            $user->image = $name;
        }
        $user->save();
        session()->flash('success', __('User updated successfully'));
        logActivity('Update User', "User '{$user->name}' ({$user->email}) updated", $user);
        return Redirect()->route('user.index');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            logActivity('Delete User', "User '{$user->name}' ({$user->email}) deleted", $user);
            $user->delete();
            session()->flash('success', __('User deleted successfully'));
        }
        return back();
    }
}
