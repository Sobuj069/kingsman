<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Purchase;

$p = Purchase::with('purchaseItems')->find(4);
foreach($p->purchaseItems as $item) {
    echo json_encode($item) . "\n";
}

$p8 = Purchase::with('purchaseItems')->find(8);
echo "Purchase 8:\n";
foreach($p8->purchaseItems as $item) {
    echo json_encode($item) . "\n";
}
