<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function ownerShip()
    {
        return $this->belongsTo(OwnerShip::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function actualPayment()
    {
        return $this->belongsTo(ActualPayment::class);
    }

    public static function getFakeSum($query, $column = 'debit')
    {
        $transactions = $query->with('invoice')->get();
        $filtered = self::filterByFakeSale($transactions);
        return $filtered->sum($column);
    }

    public static function filterByFakeSale($transactions, $orderDirection = 'desc')
    {
        if ($transactions instanceof \Illuminate\Database\Eloquent\Builder) {
            if ($orderDirection === 'asc') {
                $transactions = $transactions->with('invoice')->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
            } else {
                $transactions = $transactions->with('invoice')->orderBy('created_at', 'desc')->get();
            }
        } else if ($transactions instanceof \Illuminate\Support\Collection) {
            if ($orderDirection === 'asc') {
                $transactions = $transactions->sortBy(function($item) {
                    return sprintf('%s_%010d', $item->date ?? '', $item->id ?? 0);
                });
            } else {
                $transactions = $transactions->sortByDesc('created_at');
            }
        }

        $user = auth()->user();
        if (!$user) {
            return $transactions;
        }

        $groupedByBranch = $transactions->groupBy('invoice.branch_id');
        $filtered = collect();

        foreach ($groupedByBranch as $branchId => $branchTransactions) {
            if (!$branchId) { // Not linked to an invoice
                $filtered = $filtered->merge($branchTransactions);
                continue;
            }

            if ($user->fake_sale_percentage > 0) {
                $invoiceIds = $branchTransactions->pluck('invoice_id')->filter()->unique();
                $count = $invoiceIds->count();
                $limit = ceil(($count * $user->fake_sale_percentage) / 100);
                // Keep oldest percentage (real)
                $keepInvoiceIds = $invoiceIds->skip($limit);
                
                $filtered = $filtered->merge($branchTransactions->whereIn('invoice_id', $keepInvoiceIds));
            } else {
                $filtered = $filtered->merge($branchTransactions);
            }
        }

        if ($orderDirection === 'asc') {
            return $filtered->sortBy(function($item) {
                return sprintf('%s_%010d', $item->date ?? '', $item->id ?? 0);
            })->values();
        }

        return $filtered->sortByDesc(function($item) {
            return sprintf('%s_%010d', $item->date ?? '', $item->id ?? 0);
        })->values();
    }

}
