<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;

// 1. Calculate Total Purchase from purchases table
$total_pur = Purchase::sum('total_amount');
echo "Total Purchase from Purchase::sum: $total_pur\n";

// 2. Calculate Total Stock
$products = Product::with('unit')->get();
$avail_stock = 0;
foreach ($products as $product) {
    $fakeStockQty = product_fake_stock_val($product, 1);
    $factor = ($product->unit && $product->unit->related_value) ? $product->unit->related_value : 1;
    $purchasePrice = (float) $product->purchase_price;
    $avail_stock += $fakeStockQty * ($purchasePrice / $factor);
}
echo "Available Stock from Product fake stock: $avail_stock\n";

// 3. Let's find exactly which purchase invoices have a difference between their total_amount and their items' stock value.
$purchases = Purchase::with('purchaseItems.product.unit')->get();
foreach ($purchases as $p) {
    $itemStockValue = 0;
    foreach ($p->purchaseItems ?? [] as $item) {
        $factor = ($item->product && $item->product->unit && $item->product->unit->related_value) ? $item->product->unit->related_value : 1;
        $purchasePrice = (float) ($item->product->purchase_price ?? 0);
        $itemStockValue += $item->main_qty * ($purchasePrice / $factor);
    }
    
    $diff = $p->total_amount - $itemStockValue;
    if (round($diff, 2) != 0) {
        echo "Purchase ID: {$p->id} | Invoice Total: {$p->total_amount} | Items Stock Value: {$itemStockValue} | Diff: {$diff}\n";
    }
}
