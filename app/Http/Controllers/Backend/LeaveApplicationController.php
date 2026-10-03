<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveApplicationController extends Controller
{
    public function index()
    {
        $leave_applications = LeaveApplication::with(['employee', 'leave_type'])->orderBy('id', 'DESC')->get();
        $employees = Employee::where('status', 1)->get();
        $leave_types = LeaveType::where('status', 1)->get();
        return view('backend.pages.payroll.leave_application.index', compact('leave_applications', 'employees', 'leave_types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'leave_type_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;

        $data = $request->all();
        $data['total_days'] = $days;
        $data['status'] = 0; // Pending

        LeaveApplication::create($data);
        session()->flash('success', __('Leave Application submitted successfully'));
        return back();
    }

    public function updateStatus(Request $request, $id)
    {
        $application = LeaveApplication::findOrFail($id);
        $application->update([
            'status' => $request->status,
            'approved_by' => auth()->user()->id
        ]);
        session()->flash('success', __('Leave status updated'));
        return back();
    }

    public function destroy($id)
    {
        LeaveApplication::findOrFail($id)->delete();
        session()->flash('success', __('Leave Application deleted successfully'));
        return back();
    }
}
