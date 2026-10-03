<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leave_types = LeaveType::orderBy('id', 'DESC')->get();
        return view('backend.pages.payroll.leave_type.index', compact('leave_types'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'days' => 'required|numeric']);
        LeaveType::create($request->all());
        session()->flash('success', __('Leave Type created successfully'));
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required', 'days' => 'required|numeric']);
        LeaveType::findOrFail($id)->update($request->all());
        session()->flash('success', __('Leave Type updated successfully'));
        return back();
    }

    public function destroy($id)
    {
        LeaveType::findOrFail($id)->delete();
        session()->flash('success', __('Leave Type deleted successfully'));
        return back();
    }
}
