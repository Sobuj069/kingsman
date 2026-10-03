<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['employee', 'branch'])->orderBy('date', 'DESC');
        
        if ($request->date) {
            $query->where('date', $request->date);
        }
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $attendances = $query->get();
        $branches = Branch::all();
        $employees = Employee::where('status', 1)->get();
        
        return view('backend.pages.payroll.attendance.index', compact('attendances', 'branches', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'status' => 'required',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        Attendance::updateOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $request->date],
            [
                'branch_id' => $employee->branch_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'status' => $request->status,
                'note' => $request->note,
            ]
        );

        session()->flash('success', __('Attendance recorded successfully'));
        return back();
    }
}
