<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branchs = Branch::orderBy('id', 'desc')->get();
        return view('backend.pages.branch.index', compact('branchs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $branch = new Branch();
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->email = $request->email;
        $branch->phone = $request->phone;
        $branch->shop_name = $request->shop_name;
        $branch->created_by = auth()->user()->id;
        $image = $request->file('logo');
        $branch->save();
        if ($image) {
            $imgName = date('YmdHi') . $image->getClientOriginalName();
            $image->move('uploads/logo/', $imgName);
            $branch->logo = $imgName;
            $branch->save();
        }

        \Illuminate\Support\Facades\Cache::forget('frontend_global_showrooms');
        session()->flash('success', __('Branch created successfully'));
        logActivity('Create Branch', "Branch '{$branch->name}' created", $branch);
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $branch = Branch::find($id);
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->email = $request->email;
        $branch->phone = $request->phone;
        $branch->shop_name = $request->shop_name;
        $branch->created_by = auth()->user()->id;
        $image = $request->file('logo');
        $branch->save();
        if ($image) {
            $imgName = date('YmdHi') . $image->getClientOriginalName();
            $image->move('uploads/logo/', $imgName);
            $branch->logo = $imgName;
            $branch->save();
        }

        \Illuminate\Support\Facades\Cache::forget('frontend_global_showrooms');
        session()->flash('success', __('Branch updated successfully'));
        logActivity('Update Branch', "Branch '{$branch->name}' updated", $branch);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = Branch::find($id);
        logActivity('Delete Branch', "Branch '{$branch->name}' deleted", $branch);
        $branch->delete();
        \Illuminate\Support\Facades\Cache::forget('frontend_global_showrooms');
        session()->flash('success', __('Branch deleted successfully'));
        return back();
    }

    // public function switch(Request $request)
    // {
    //     $request->validate([
    //         'branch_id' => 'required|exists:branches,id'
    //     ]);

    //     Session::put('branch_id', $request->branch_id);

    //     return back()->with('success', 'Branch switched successfully!');
    // }

    public function switch(Request $request)
    {
        if (!is_branch_switch_enabled() || !check_permission('switch.branch')) {
            session()->flash('error', __('Branch switching has been disabled by administrator.'));
            return back();
        }

        if ($request->branch_id) {
            session(['branch_filter_id' => $request->branch_id]);
        } else {
            session()->forget('branch_filter_id');
        }
        return back(); // আগের পেইজে ফেরত যাবে
    }
}
