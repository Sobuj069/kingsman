<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Models\Purchase;
use App\Models\PurchaseItem;

echo "=== PURCHASES ===\n";
$purchases = Purchase::orderBy('id')->get();
foreach ($purchases as $p) {
    echo "Purchase#{$p->id} purchase_no={$p->purchase_no} date={$p->date} total_amount={$p->total_amount}\n";
}

echo "\n=== PURCHASE ITEMS ===\n";
$items = PurchaseItem::orderBy('purchase_id')->orderBy('id')->get();
foreach ($items as $item) {
    echo "PurItem#{$item->id} purchase_id={$item->purchase_id} product_id={$item->product_id} rate={$item->rate} main_qty={$item->main_qty} stock_qty={$item->stock_qty}\n";
}
