<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\InvoiceItem;

// Get all unique product+branch combos that have invoice items
$combos = InvoiceItem::select('product_id', 'branch_id')
    ->groupBy('product_id', 'branch_id')
    ->get();

echo "Found " . $combos->count() . " product-branch combos to recalculate.\n\n";

foreach ($combos as $combo) {
    $productId = $combo->product_id;
    $branchId  = $combo->branch_id;

    $product = Product::find($productId);
    if (!$product || $product->is_service == 1) {
        continue;
    }

    // Get purchase items ordered oldest first (FIFO)
    $purchaseItems = PurchaseItem::where('product_id', $productId)
        ->where('branch_id', $branchId)
        ->orderBy('id', 'asc')
        ->get();

    if ($purchaseItems->isEmpty()) {
        echo "No purchase items for product #{$productId} branch #{$branchId}, skipping.\n";
        continue;
    }

    $factor = ($product->unit && $product->unit->related_value) ? $product->unit->related_value : 1;

    // Set temp_qty = full original quantity for each purchase item
    foreach ($purchaseItems as $pi) {
        if ($product->unit && $product->unit->related_unit == null) {
            $pi->temp_qty = $pi->main_qty;
        } else {
            $pi->temp_qty = ($pi->main_qty * $factor) + $pi->sub_qty;
        }
    }

    // Get invoice items ordered oldest first for FIFO consumption
    $invoiceItems = InvoiceItem::where('product_id', $productId)
        ->where('branch_id', $branchId)
        ->orderBy('date', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    foreach ($invoiceItems as $ii) {
        if ($product->unit && $product->unit->related_unit == null) {
            $saleQty = $ii->main_qty;
        } else {
            $saleQty = ($ii->main_qty * $factor) + ($ii->sub_qty ?? 0);
        }

        $remainingQty = $saleQty;
        $totalCost    = 0;

        foreach ($purchaseItems as $pi) {
            if ($remainingQty <= 0) break;
            $availableQty = $pi->temp_qty;
            if ($availableQty <= 0) continue;

            $usedQty = min($remainingQty, $availableQty);

            if ($product->unit && $product->unit->related_unit == null) {
                $totalCost += $usedQty * $pi->rate;
            } else {
                $totalCost += ($usedQty / $factor) * $pi->rate;
            }

            $pi->temp_qty  -= $usedQty;
            $remainingQty  -= $usedQty;
        }

        // If stock exhausted before sale qty (shouldn't happen normally)
        if ($remainingQty > 0 && $purchaseItems->isNotEmpty()) {
            $lastPi = $purchaseItems->last();
            if ($product->unit && $product->unit->related_unit == null) {
                $totalCost += $remainingQty * $lastPi->rate;
            } else {
                $totalCost += ($remainingQty / $factor) * $lastPi->rate;
            }
        }

        $oldPurSubtotal = $ii->pur_subtotal;
        if (abs($oldPurSubtotal - $totalCost) > 0.001) {
            echo "Invoice Item #{$ii->id} (product #{$productId}): pur_subtotal {$oldPurSubtotal} => {$totalCost}\n";
            $ii->pur_subtotal = $totalCost;
            $ii->save();
        }
    }
}

echo "\nDone! All pur_subtotal values have been recalculated.\n";
