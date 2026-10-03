<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'branch_id', 'month', 'year', 
        'absent_days', 'late_days', 'present_days', 'leave_days',
        'overtime_hours', 'overtime_rate', 'overtime_amount',
        'present_amount', 'gross_salary', 'bonus', 'deduction', 'net_pay',
        'status', 'payment_date', 'bank_id', 'created_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bank()
    {
        return $this->belongsTo(BankAccount::class, 'bank_id');
    }
}
