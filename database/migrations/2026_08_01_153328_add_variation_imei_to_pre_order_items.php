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
        Schema::table('pre_order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variation_id')->nullable()->after('product_id');
            $table->string('imei')->nullable()->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_order_items', function (Blueprint $table) {
            $table->dropColumn(['product_variation_id', 'imei']);
        });
    }
};
