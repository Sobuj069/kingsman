<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'father_name',
        'mother_name',
        'date',
        'email',
        'phone',
        'address',
        'nid',
        'dob',
        'image',
        'joining_date',
        'department_id',
        'designation_id',
        'salary',
        'payment_date',
        'advance_payment',
        'commission',
        'salary_pay',
        'gender',
        'status',
        'created_by'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function bank()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
