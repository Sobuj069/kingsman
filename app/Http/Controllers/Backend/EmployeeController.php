<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\BankTransaction;
use App\Models\EmployeePayments;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        $query = Employee::orderBy('id', 'DESC');

        if($userBranchId == 1){
            if($filterBranchId){
                $query->where('branch_id', $filterBranchId);
            }
        }else{
            $query->where('branch_id', $userBranchId);
        }

        if ($request->name != null) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->phone != null) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $employees = $query->paginate(20)->appends($request->all());
        $bankAccounts = BankAccount::orderBy('id', 'DESC')->get();
        $allBranch = Branch::orderBy('id', 'asc')->get();
        
        $name = $request->name;
        $phone = $request->phone;

        return view('backend.pages.employee.index', compact('employees', 'bankAccounts', 'allBranch', 'name', 'phone'));
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
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);

        $employee = new Employee();
        $employee->date = date('Y-m-d');
        if (auth()->user()->branch_id == 1) {
            $employee->branch_id = $request->branch_id;
        } else {
            $employee->branch_id = auth()->user()->branch_id;
        }
        $employee->name = $request->name;
        $employee->father_name = $request->father_name;
        $employee->mother_name = $request->mother_name;
        $employee->phone = $request->phone;
        $employee->email = $request->email;
        $employee->nid = $request->nid;
        $employee->dob = $request->dob;
        $employee->address = $request->address;
        $employee->joining_date = $request->joining_date;
        $employee->department_id = $request->department_id;
        $employee->designation_id = $request->designation_id;
        $employee->gender = $request->gender;
        $employee->salary = $request->salary;
        $employee->commission = $request->commission;
        $employee->created_by = auth()->user()->id;
        $employee->save();

        session()->flash('success', __('Employee Create successfully'));
        return back();
    }

    public function salaryDetails($id)
    {
        $employee = Employee::where('id',$id)->first();
        $employee_payment = EmployeePayments::where('employee_id', $employee->id)->orderBy('id', 'DESC')->get();
        return view('backend.pages.employee.details', compact('employee', 'employee_payment'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function PaymentStore(Request $request, $id)
    {
        // dd($request->all());
        $employee = Employee::find($id);
        $staff_payment = EmployeePayments::where('employee_id', $employee->id)->where('month', Carbon::now()->startOfMonth())->first();

        if ($request->payment_type == 'Advance') {
            $payment_date = $request->payment_date;
            $today_date = Carbon::now()->format('Y-m-d');

            $oldMonth = Carbon::parse($employee->payment_date)->format('Y-m');
            $newMonth = Carbon::parse($payment_date)->format('Y-m');

            if ($oldMonth != $newMonth) {
                // New month: reset and create new salary record
                $employee->update([
                    'payment_date' => $payment_date,
                    'advance_payment' => $request->payment_amount,
                    'salary_pay' => $request->payment_amount,
                ]);

                EmployeePayments::create([
                    'employee_id' => $employee->id,
                    'date' => date('Y-m-d'),
                    'branch_id' => $employee->branch_id,
                    'month' => Carbon::parse($payment_date)->startOfMonth(),
                    'payment_type' => $request->payment_type,
                    'payment' => $request->payment_amount,
                    'bank_id' => $request->payment_method,
                    'payment_date' => $request->payment_date,
                    'created_by' => auth()->user()->id,
                    'note' => $request->note,
                ]);
            } else {
                // Same month: add to existing values
                // dd($staf->advance_payment);
                $employee->update([
                    'payment_date' => $request->payment_date,
                    'advance_payment' => $employee->advance_payment + $request->payment_amount,
                    'salary_pay' => $employee->salary_pay + $request->payment_amount,
                ]);

                EmployeePayments::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => date('Y-m-d'),
                        'branch_id' => $employee->branch_id,
                        'month' => Carbon::parse($payment_date)->startOfMonth(),
                        'payment_date' => $request->payment_date,
                        'payment' => $request->payment_amount,
                        'bank_id' => $request->payment_method,
                        'payment_type' => $request->payment_type,
                        'created_by' => auth()->user()->id,
                        'note' => $request->note,
                    ]
                );
            }

            $transaction = new Transaction();
            $transaction->transaction_type = 'Employe Advance Payment';
            $transaction->bank_id = $request->payment_method;
            $transaction->date = $request->payment_date;
            $transaction->branch_id = $employee->branch_id;
            $transaction->employee_id = $employee->id;
            $transaction->debit = $request->payment_amount;
            $transaction->credit = NULL;
            $transaction->created_by = auth()->user()->id;
            $transaction->save();

            $bank_transaction = new BankTransaction();
            $bank_transaction->trans_type = 'withdraw';
            $bank_transaction->pay_type = 'emp_pay';
            $bank_transaction->date = $request->payment_date;
            $bank_transaction->branch_id = $employee->branch_id;
            $bank_transaction->bank_id = $request->payment_method;
            $bank_transaction->employee_id = $employee->id;
            $bank_transaction->amount = $request->payment_amount;
            $bank_transaction->created_by = auth()->user()->id;
            $bank_transaction->save();
        }

        // salary saction baki ase 

        if ($request->payment_type == 'Salary') {

            $staf_payment_date = $employee->payment_date;
            $payment_date = $request->payment_date;

            // Convert dates to Carbon
            $oldMonth = Carbon::parse($staf_payment_date)->format('Y-m');
            $newMonth = Carbon::parse($payment_date)->format('Y-m');

            if ($oldMonth != $newMonth) {
                // New month payment: Reset salary record
                $employee->update([
                    'payment_date' => $payment_date,
                    'advance_payment' => 0,
                    'salary_pay' => $employee->advance_payment + $request->payment_amount,
                ]);

                EmployeePayments::create([

                    'employee_id' => $employee->id,
                    'date' => date('Y-m-d'),
                    'branch_id' => $employee->branch_id,
                    'month' => Carbon::parse($payment_date)->startOfMonth(),
                    'payment_type' => $request->payment_type,
                    'payment' => $request->payment_amount,
                    'bank_id' => $request->payment_method,
                    'payment_date' => $request->payment_date,
                    'created_by' => auth()->user()->id,
                    'note' => $request->note,
                ]);
                

                // If payment completed for the month, reset
                if ($employee->salary == $employee->salary_pay) {
                    $employee->update([
                        'payment_date' => $payment_date,
                        'advance_payment' => 0,
                        'salary_pay' => 0,
                    ]);
                }
            } else {
                // Same month: just update existing
                $employee->update([
                    'salary_pay' => $employee->salary_pay + $request->payment_amount,
                ]);

                EmployeePayments::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => date('Y-m-d'),
                        'branch_id' => $employee->branch_id,
                        'month' => Carbon::parse($payment_date)->startOfMonth(),
                        'payment_date' => $request->payment_date,
                        'payment' => $request->payment_amount,
                        'bank_id' => $request->payment_method,
                        'payment_type' => $request->payment_type,
                        'created_by' => auth()->user()->id,
                        'note' => $request->note,
                    ]
                );
            }

            $transaction = new Transaction();
            $transaction->transaction_type = 'Employe Payment';
            $transaction->bank_id = $request->payment_method;
            $transaction->date = $request->payment_date;
            $transaction->branch_id = $employee->branch_id;
            $transaction->employee_id = $employee->id;
            $transaction->debit = $request->payment_amount;
            $transaction->credit = NULL;
            $transaction->created_by = auth()->user()->id;
            $transaction->save();

            $bank_transaction = new BankTransaction();
            $bank_transaction->trans_type = 'withdraw';
            $bank_transaction->pay_type = 'emp_pay';
            $bank_transaction->date = $request->payment_date;
            $bank_transaction->bank_id = $request->payment_method;
            $bank_transaction->branch_id = $employee->branch_id;
            $bank_transaction->employee_id = $employee->id;
            $bank_transaction->amount = $request->payment_amount;
            $bank_transaction->created_by = auth()->user()->id;
            $bank_transaction->save();
        }

        session()->flash('success', __('Employe Payment successfully'));
        return back();
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
        $employee = Employee::find($id);
        $employee->update([
            'name' => $request->name,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nid' => $request->nid,
            'dob' => $request->dob,
            'address' => $request->address,
            'gender' => $request->gender,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'salary' => $request->salary,
            'commission' => $request->commission,
        ]);
        session()->flash('success', __('Employee updated successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employe = Employee::find($id);
        $employe->delete();
        session()->flash('success', __('Employee deleted successfully'));
        return back();
    }
}
