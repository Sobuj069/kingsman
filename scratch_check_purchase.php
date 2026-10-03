<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Purchase;

$purchases = Purchase::all();
foreach($purchases as $p) {
    echo "ID: {$p->id} | Total: {$p->total_amount} | Discount: {$p->discount_amount} | Shipping: {$p->shipping_cost} | Labor: {$p->labor_cost} | Others: {$p->other_cost} | Note: {$p->note}\n";
}
