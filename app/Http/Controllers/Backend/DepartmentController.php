<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('id', 'DESC')->get();
        return view('backend.pages.payroll.department.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Department::create($request->all());
        session()->flash('success', __('Department created successfully'));
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);
        $department = Department::findOrFail($id);
        $department->update($request->all());
        session()->flash('success', __('Department updated successfully'));
        return back();
    }

    public function destroy($id)
    {
        Department::findOrFail($id)->delete();
        session()->flash('success', __('Department deleted successfully'));
        return back();
    }
}
