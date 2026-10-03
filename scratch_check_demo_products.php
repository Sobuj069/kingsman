<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$allProducts = \App\Models\Product::get();
echo "Total Products in DB: " . $allProducts->count() . "\n";

// Check products with invoices or purchases
$invoiceProductIds = \App\Models\InvoiceItem::pluck('product_id')->unique()->toArray();
$purchaseProductIds = \App\Models\PurchaseItem::pluck('product_id')->unique()->toArray();
$serialProductIds = \App\Models\SerialNumber::pluck('product_id')->unique()->toArray();

$usedProductIds = array_unique(array_merge($invoiceProductIds, $purchaseProductIds, $serialProductIds));
echo "Products used in Invoices/Purchases/Serials: " . count($usedProductIds) . "\n";

foreach ($allProducts as $p) {
    if (in_array($p->id, $usedProductIds) || $p->id == 5643 || $p->id == 5644) {
        echo "[KEEP] Product ID: {$p->id} | Name: {$p->name} | Barcode: {$p->barcode}\n";
    }
}
