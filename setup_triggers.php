<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\PurchaseItem;

try {
    // 1. Sync all existing products
    $products = Product::all();
    foreach ($products as $product) {
        $stock = PurchaseItem::where('product_id', $product->id)->sum('stock_qty');
        $product->main_qty = $stock;
        $product->save();
    }
    echo "Synced " . $products->count() . " products.\n";

    // 2. Drop existing triggers if any
    DB::unprepared('DROP TRIGGER IF EXISTS sync_product_main_qty_after_insert');
    DB::unprepared('DROP TRIGGER IF EXISTS sync_product_main_qty_after_update');
    DB::unprepared('DROP TRIGGER IF EXISTS sync_product_main_qty_after_delete');

    // 3. Create triggers
    DB::unprepared('
        CREATE TRIGGER sync_product_main_qty_after_insert
        AFTER INSERT ON purchase_items
        FOR EACH ROW
        BEGIN
            UPDATE products 
            SET main_qty = (SELECT COALESCE(SUM(stock_qty), 0) FROM purchase_items WHERE product_id = NEW.product_id)
            WHERE id = NEW.product_id;
        END;
    ');

    DB::unprepared('
        CREATE TRIGGER sync_product_main_qty_after_update
        AFTER UPDATE ON purchase_items
        FOR EACH ROW
        BEGIN
            UPDATE products 
            SET main_qty = (SELECT COALESCE(SUM(stock_qty), 0) FROM purchase_items WHERE product_id = NEW.product_id)
            WHERE id = NEW.product_id;
            
            IF NEW.product_id <> OLD.product_id THEN
                UPDATE products 
                SET main_qty = (SELECT COALESCE(SUM(stock_qty), 0) FROM purchase_items WHERE product_id = OLD.product_id)
                WHERE id = OLD.product_id;
            END IF;
        END;
    ');

    DB::unprepared('
        CREATE TRIGGER sync_product_main_qty_after_delete
        AFTER DELETE ON purchase_items
        FOR EACH ROW
        BEGIN
            UPDATE products 
            SET main_qty = (SELECT COALESCE(SUM(stock_qty), 0) FROM purchase_items WHERE product_id = OLD.product_id)
            WHERE id = OLD.product_id;
        END;
    ');

    echo "Triggers created successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
