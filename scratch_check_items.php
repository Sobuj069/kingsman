<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Purchase;

$purchases = Purchase::with('purchaseItems.product')->get(); // let's try purchaseItems, maybe items is wrong.

foreach($purchases as $p) {
    echo "Purchase ID: {$p->id}\n";
    foreach($p->purchaseItems ?? $p->items ?? [] as $item) {
        echo " - Item: ProdID: {$item->product_id}, Qty: {$item->qty}, Price: {$item->purchase_price}\n";
    }
}
