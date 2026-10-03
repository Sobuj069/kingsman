<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function () {
            Product::clearFrontendCache();
        });
        static::deleted(function () {
            Product::clearFrontendCache();
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSuppliersAttribute()
    {
        if ($this->imei) {
            $imeis = array_map('trim', explode(',', $this->imei));
            if (!empty($imeis)) {
                $purchaseIds = \App\Models\SerialNumber::where('product_id', $this->product_id)
                    ->whereIn('serial', $imeis)
                    ->pluck('purchase_id')
                    ->filter()
                    ->unique();
                if ($purchaseIds->isNotEmpty()) {
                    $supplierIds = \App\Models\Purchase::whereIn('id', $purchaseIds)
                        ->pluck('supplier_id')
                        ->filter()
                        ->unique();
                    if ($supplierIds->isNotEmpty()) {
                        return \App\Models\Supplier::whereIn('id', $supplierIds)->get();
                    }
                }
            }
        }

        $query = \App\Models\PurchaseItem::where('product_id', $this->product_id)
            ->with('purchase.supplier');
        if ($this->product_variation_id) {
            $query->where('product_variation_id', $this->product_variation_id);
        }
        $latestPurchase = $query->latest('id')->first();
        if ($latestPurchase && $latestPurchase->purchase && $latestPurchase->purchase->supplier) {
            return collect([$latestPurchase->purchase->supplier]);
        }

        return collect();
    }

    public function product_variation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function size()
    {
        return $this->belongsTo('App\Models\ProductSize','size_id','id');
    }

    public function color()
    {
        return $this->belongsTo('App\Models\ProductColor','color_id','id');
    }

    public static function getFakeSum($query, $column = 'inv_subtotal')
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

        $grouped = $items->groupBy('invoice.branch_id');
        $filtered = collect();

        foreach ($grouped as $branchId => $branchItems) {
            if (!$branchId) { // Not linked to an invoice (rare, but safety)
                $filtered = $filtered->merge($branchItems);
                continue;
            }

            if ($user->fake_sale_percentage > 0) {
                // Number of invoices to show for this branch
                $invoiceIds = $branchItems->pluck('invoice_id')->unique();
                $count = $invoiceIds->count();
                $limit = ceil(($count * $user->fake_sale_percentage) / 100);
                
                // Which invoices are we keeping? (Skip top percentage, which are fake due to sortByDesc)
                $keepInvoiceIds = $invoiceIds->skip($limit);
                
                // Keep only items that belong to these invoices
                $filtered = $filtered->merge($branchItems->whereIn('invoice_id', $keepInvoiceIds));
            } else {
                $filtered = $filtered->merge($branchItems);
            }
        }
        return $filtered;
    }
}
