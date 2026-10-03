<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\SalarySheet;
use App\Models\Attendance;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    // Salary Sheet List
    public function index(Request $request)
    {
        $query = SalarySheet::with(['employee', 'branch'])->orderBy('id', 'DESC');

        if ($request->month) {
            $query->where('month', $request->month);
        }
        if ($request->year) {
            $query->where('year', $request->year);
        }
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $salary_sheets = $query->paginate(20);
        $branches = Branch::all();
        
        return view('backend.pages.payroll.salary_sheet.index', compact('salary_sheets', 'branches'));
    }

    // Generate Salary Sheet View
    public function create()
    {
        $branches = Branch::all();
        $years = range(date('Y') - 1, date('Y') + 1);
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        return view('backend.pages.payroll.salary_sheet.create', compact('branches', 'years', 'months'));
    }

    // Process Salary Generation
    public function generate(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);

        $branch_id = $request->branch_id;
        $month = $request->month;
        $year = $request->year;

        // Check if already generated
        $exists = SalarySheet::where('branch_id', $branch_id)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

        if ($exists) {
            session()->flash('error', __('Salary sheet already generated for this month and branch'));
            return back();
        }

        $employees = Employee::where('branch_id', $branch_id)->where('status', 1)->get();

        if ($employees->isEmpty()) {
            session()->flash('error', __('No active employees found in this branch'));
            return back();
        }

        foreach ($employees as $employee) {
            $absent_days = Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('status', 'Absent')
                ->count();
            
            $present_days = Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('status', 'Present')
                ->count();

            $late_days = Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('status', 'Late')
                ->count();

            SalarySheet::create([
                'employee_id' => $employee->id,
                'branch_id' => $branch_id,
                'month' => $month,
                'year' => $year,
                'absent_days' => $absent_days,
                'present_days' => $present_days,
                'late_days' => $late_days,
                'gross_salary' => $employee->salary,
                'net_pay' => $employee->salary, // Initial net pay
                'status' => 0, // Pending
                'created_by' => auth()->user()->id,
            ]);
        }

        session()->flash('success', __('Salary sheet generated successfully'));
        return redirect()->route('payroll.salary-sheet.index');
    }

    // Pay Salary
    public function pay(Request $request, $id)
    {
        $sheet = SalarySheet::findOrFail($id);
        
        if ($sheet->status == 1) {
            session()->flash('error', __('Salary already paid'));
            return back();
        }

        $request->validate([
            'bank_id' => 'required',
            'payment_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $sheet->update([
                'status' => 1,
                'payment_date' => $request->payment_date,
                'bank_id' => $request->bank_id,
            ]);

            // Transaction
            $transaction = new Transaction();
            $transaction->transaction_type = 'Salary Payment';
            $transaction->bank_id = $request->bank_id;
            $transaction->date = $request->payment_date;
            $transaction->branch_id = $sheet->branch_id;
            $transaction->employee_id = $sheet->employee_id;
            $transaction->debit = $sheet->net_pay;
            $transaction->created_by = auth()->user()->id;
            $transaction->save();

            // Bank Transaction
            $bank_transaction = new BankTransaction();
            $bank_transaction->trans_type = 'withdraw';
            $bank_transaction->pay_type = 'emp_pay';
            $bank_transaction->date = $request->payment_date;
            $bank_transaction->bank_id = $request->bank_id;
            $bank_transaction->branch_id = $sheet->branch_id;
            $bank_transaction->employee_id = $sheet->employee_id;
            $bank_transaction->amount = $sheet->net_pay;
            $bank_transaction->created_by = auth()->user()->id;
            $bank_transaction->save();

            DB::commit();
            session()->flash('success', __('Salary paid successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('Something went wrong: ' . $e->getMessage()));
        }

        return back();
    }

    // Update Sheet (Absent, Overtime, etc.)
    public function update(Request $request, $id)
    {
        $sheet = SalarySheet::findOrFail($id);
        $sheet->update($request->only([
            'absent_days', 'late_days', 'present_days', 'leave_days',
            'overtime_hours', 'overtime_rate', 'overtime_amount',
            'bonus', 'deduction', 'net_pay'
        ]));
        
        session()->flash('success', __('Salary sheet updated'));
        return back();
    }
}
