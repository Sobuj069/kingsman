<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Purchase;

$p = Purchase::with('purchaseItems.product.unit')->find(4);
echo "Purchase 4 Total Amount: {$p->total_amount}\n";
echo "Purchase 4 Discount: {$p->discount_amount}\n";
echo "Purchase 4 Shipping: {$p->shipping_cost}\n";
echo "Purchase 4 Labor: {$p->labor_cost}\n";
echo "Purchase 4 Others: {$p->other_cost}\n";

$sum = 0;
foreach($p->purchaseItems as $item) {
    echo "Item ProdID: {$item->product_id} | Qty: {$item->main_qty} | Rate: {$item->rate} | SubTotal: {$item->subtotal}\n";
    $sum += $item->subtotal;
}
echo "Sum of Subtotals: {$sum}\n";
