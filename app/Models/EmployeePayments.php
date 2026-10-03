<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayments extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'date','branch_id','created_by','note','bank_id', 'month', 'salary','salary_pay', 'payment', 'advance_payment','payment_type','payment_date'];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function bank_account()
    {
        return $this->belongsTo(BankAccount::class,'bank_id','id');
    }
}
