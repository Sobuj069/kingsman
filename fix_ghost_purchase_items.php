<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\InvoiceItem;

// STEP 1: Remove duplicate/orphaned purchase items for same purchase+product
$groups = PurchaseItem::select('purchase_id', 'product_id')
    ->groupBy('purchase_id', 'product_id')
    ->havingRaw('COUNT(*) > 1')
    ->get();

echo "=== STEP 1: Removing orphaned duplicate purchase items ===\n";
echo "Found " . $groups->count() . " duplicate product entries in purchases.\n";

foreach ($groups as $group) {
    $items = PurchaseItem::where('purchase_id', $group->purchase_id)
        ->where('product_id', $group->product_id)
        ->orderBy('id', 'asc')
        ->get();

    echo "\nPurchase #{$group->purchase_id}, Product #{$group->product_id}: " . $items->count() . " items\n";
    foreach ($items as $item) {
        echo "  PurItem#{$item->id} rate={$item->rate} main_qty={$item->main_qty} stock_qty={$item->stock_qty}\n";
    }

    // Keep only the LATEST item (highest ID = most recent after edit)
    $toDelete = $items->slice(0, $items->count() - 1);
    foreach ($toDelete as $old) {
        echo "  => DELETING orphaned PurItem#{$old->id} (rate={$old->rate})\n";
        $old->delete();
    }
}

// STEP 2: Recalculate pur_subtotal via FIFO
echo "\n=== STEP 2: Recalculating pur_subtotal via FIFO ===\n";

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

    $purchaseItems = PurchaseItem::where('product_id', $productId)
        ->where('branch_id', $branchId)
        ->orderBy('id', 'asc')
        ->get();

    if ($purchaseItems->isEmpty()) {
        echo "No purchase items for product #{$productId} branch #{$branchId}, skipping.\n";
        continue;
    }

    $factor = ($product->unit && $product->unit->related_value) ? $product->unit->related_value : 1;

    foreach ($purchaseItems as $pi) {
        if ($product->unit && $product->unit->related_unit == null) {
            $pi->temp_qty = $pi->main_qty;
        } else {
            $pi->temp_qty = ($pi->main_qty * $factor) + $pi->sub_qty;
        }
    }

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

echo "\nAll done!\n";
