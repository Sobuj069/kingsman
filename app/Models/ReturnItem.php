<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function return()
    {
        return $this->belongsTo(ReturnTbl::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class)->select('id', 'name', 'phone', 'email', 'address');
    }

    //created by
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->select('id', 'name');
    }

    public static function filterByFakeSale($items)
    {
        if ($items instanceof \Illuminate\Database\Eloquent\Builder) {
            $items = $items->with('return.invoice')->get();
        }

        $grouped = $items->groupBy('return.invoice.created_by');
        $filtered = collect();

        foreach ($grouped as $userId => $userItems) {
            if (!$userId) { 
                $filtered = $filtered->merge($userItems);
                continue;
            }

            $user = \App\Models\User::find($userId);
            if ($user && $user->fake_sale_percentage > 0) {
                $invoiceIds = $userItems->pluck('return.invoice_id')->filter()->unique();
                $count = $invoiceIds->count();
                $limit = ceil(($count * $user->fake_sale_percentage) / 100);
                $keepInvoiceIds = $invoiceIds->skip($limit);
                
                $filtered = $filtered->merge($userItems->whereIn('return.invoice_id', $keepInvoiceIds));
            } else {
                $filtered = $filtered->merge($userItems);
            }
        }
        return $filtered;
    }
}
