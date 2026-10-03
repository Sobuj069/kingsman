<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::orderBy('id', 'DESC')->get();
        return view('backend.pages.payroll.designation.index', compact('designations'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Designation::create($request->all());
        session()->flash('success', __('Designation created successfully'));
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);
        $designation = Designation::findOrFail($id);
        $designation->update($request->all());
        session()->flash('success', __('Designation updated successfully'));
        return back();
    }

    public function destroy($id)
    {
        Designation::findOrFail($id)->delete();
        session()->flash('success', __('Designation deleted successfully'));
        return back();
    }
}
