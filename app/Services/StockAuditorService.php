<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\InvoiceItem;
use App\Models\ReturnItem;
use App\Models\ReturnPurchaseItem;
use App\Models\DamageItem;
use App\Models\AdjustStockItem;
use App\Models\TransferItem;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class StockAuditorService
{
    /**
     * Audit a specific product for stock discrepancies.
     */
    public function auditProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $relatedValue = ($product->unit && $product->unit->related_value) ? $product->unit->related_value : 1;

        $getSum = function($modelClass, $condition = []) use ($productId, $relatedValue) {
            $query = $modelClass::where('product_id', $productId);
            foreach($condition as $col => $val) {
                $query->where($col, $val);
            }
            $items = $query->get(['main_qty', 'sub_qty']);
            $total = 0;
            foreach ($items as $item) {
                $total += (($item->main_qty ?? 0) * $relatedValue) + ($item->sub_qty ?? 0);
            }
            return $total;
        };

        // 1. Calculate Inflows
        $purchases = $getSum(PurchaseItem::class);
        $salesReturns = $getSum(ReturnItem::class);
        $adjustIn = $getSum(AdjustStockItem::class, ['stock_status' => 1]);
        $transferIn = $getSum(TransferItem::class, ['status' => 1]); // Assuming status 1 is received

        // 2. Calculate Outflows
        $sales = $getSum(InvoiceItem::class);
        $purchaseReturns = $getSum(ReturnPurchaseItem::class);
        $damages = $getSum(DamageItem::class);
        $adjustOut = $getSum(AdjustStockItem::class, ['stock_status' => 0]);
        $transferOut = $getSum(TransferItem::class, ['status' => 0]); // Assuming status 0 is sent

        // 3. Theoretical Stock Calculation
        $theoreticalStock = ($purchases + $salesReturns + $adjustIn + $transferIn) 
                          - ($sales + $purchaseReturns + $damages + $adjustOut + $transferOut);

        $actualStock = $product->main_qty ?? 0;
        $discrepancy = $actualStock - $theoreticalStock;
        // 4. Fetch Activity Logs for Context
        $logs = ActivityLog::where('model_type', 'Product')
            ->where('model_id', $productId)
            ->orWhere(function($query) use ($productId) {
                $query->whereIn('model_type', ['PurchaseItem', 'InvoiceItem', 'AdjustStockItem'])
                      ->where('data', 'like', '%"product_id":' . $productId . '%');
            })
            ->latest()
            ->limit(20)
            ->get();

        $actualStock = (float)$product->main_qty;
        $discrepancy = $theoreticalStock - $actualStock;

        // Format stock helper
        $formatStock = function($qty) use ($product) {
            if ($product->unit && $product->unit->related_unit_id && $product->unit->related_value) {
                $mainUnitName = $product->unit->name;
                $subUnit = \App\Models\Unit::find($product->unit->related_unit_id);
                $subUnitName = $subUnit ? $subUnit->name : '';
                $relatedValue = $product->unit->related_value;
                
                $mainQty = floor(abs($qty) / $relatedValue);
                $subQty = abs($qty) % $relatedValue;
                
                $sign = $qty < 0 ? '-' : '';
                
                if ($mainQty > 0 && $subQty > 0) {
                    return $sign . $mainQty . ' ' . $mainUnitName . ' ' . $subQty . ' ' . $subUnitName;
                } elseif ($mainQty > 0) {
                    return $sign . $mainQty . ' ' . $mainUnitName;
                } elseif ($subQty > 0) {
                    return $sign . $subQty . ' ' . $subUnitName;
                } else {
                    return '0 ' . $mainUnitName;
                }
            }
            return $qty . ' ' . ($product->unit->name ?? '');
        };

        return [
            'product' => $product,
            'theoretical_stock' => $theoreticalStock,
            'actual_stock' => $actualStock,
            'discrepancy' => $discrepancy,
            'theoretical_stock_formatted' => $formatStock($theoreticalStock),
            'actual_stock_formatted' => $formatStock($actualStock),
            'discrepancy_formatted' => $formatStock($discrepancy),
            'details' => [
                'purchases' => $purchases,
                'sales' => $sales,
                'sales_returns' => $salesReturns,
                'purchase_returns' => $purchaseReturns,
                'damages' => $damages,
                'adjust_in' => $adjustIn,
                'adjust_out' => $adjustOut,
                'transfer_in' => $transferIn,
                'transfer_out' => $transferOut,
            ],
            'logs' => $logs
        ];
    }

    /**
     * Fix the stock discrepancy for a specific product.
     */
    public function fixStock($productId)
    {
        $audit = $this->auditProduct($productId);
        $product = Product::findOrFail($productId);
        
        // Update the main_qty in products table
        $product->main_qty = $audit['theoretical_stock'];
        $product->save();

        return $audit['theoretical_stock'];
    }

    /**
     * Get a list of all products with discrepancies.
     */
    public function getDiscrepancyList()
    {
        $products = Product::all();
        $discrepancies = [];

        foreach ($products as $product) {
            $audit = $this->auditProduct($product->id);
            if ($audit['discrepancy'] != 0) {
                $discrepancies[] = $audit;
            }
        }

        return $discrepancies;
    }
}
