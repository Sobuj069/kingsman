<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variations', 'selling_price')) {
                $table->decimal('selling_price', 12, 2)->nullable()->after('image');
            }
            if (!Schema::hasColumn('product_variations', 'dis_selling_price')) {
                $table->decimal('dis_selling_price', 12, 2)->nullable()->after('selling_price');
            }
            if (!Schema::hasColumn('product_variations', 'purchase_price')) {
                $table->decimal('purchase_price', 12, 2)->nullable()->after('dis_selling_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('product_variations', 'selling_price')) {
                $cols[] = 'selling_price';
            }
            if (Schema::hasColumn('product_variations', 'dis_selling_price')) {
                $cols[] = 'dis_selling_price';
            }
            if (Schema::hasColumn('product_variations', 'purchase_price')) {
                $cols[] = 'purchase_price';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
