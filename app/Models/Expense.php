<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function bank_account()
    {
        return $this->belongsTo(BankAccount::class, 'bank_id');
    }

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
