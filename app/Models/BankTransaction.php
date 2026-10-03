<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bank_account()
    {
        return $this->belongsTo(BankAccount::class, 'bank_id');
    }

    public function to_bank_account()
    {
        return $this->belongsTo(BankAccount::class, 'to_bank_id');
    }

    public function from_bank_account()
    {
        return $this->belongsTo(BankAccount::class, 'from_bank_id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getFakeSum($query, $column = 'amount')
    {
        $items = $query->with('invoice')->get();
        return self::filterByFakeSale($items)->sum($column);
    }

    public static function filterByFakeSale($items)
    {
        if ($items instanceof \Illuminate\Database\Eloquent\Builder) {
            $items = $items->with('invoice')->orderBy('created_at', 'desc')->get();
        } else if ($items instanceof \Illuminate\Support\Collection) {
            $items = $items->sortByDesc('created_at');
        }

        $user = auth()->user();
        if (!$user) {
            return $items;
        }

        $groupedByBranch = $items->groupBy('invoice.branch_id');
        $filtered = collect();

        foreach ($groupedByBranch as $branchId => $branchItems) {
            if (!$branchId) { // Transactions not linked to invoices (e.g., expenses, purchases)
                $filtered = $filtered->merge($branchItems);
                continue;
            }

            if ($user->fake_sale_percentage > 0) {
                // Number of invoices to show for this branch
                $invoiceIds = $branchItems->pluck('invoice_id')->filter()->unique();
                $count = $invoiceIds->count();
                $limit = ceil(($count * $user->fake_sale_percentage) / 100);
                
                // Which invoices are we keeping? (Skip top percentage, which are fake due to sortByDesc)
                $keepInvoiceIds = $invoiceIds->skip($limit);
                
                // Keep only transactions that belong to these invoices
                $filtered = $filtered->merge($branchItems->whereIn('invoice_id', $keepInvoiceIds));
            } else {
                $filtered = $filtered->merge($branchItems);
            }
        }
        return $filtered;
    }
}
