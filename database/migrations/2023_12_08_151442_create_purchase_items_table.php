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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variation_id')->nullable();
            $table->decimal('rate', 10, 2)->default(0.00);
            $table->decimal('main_qty', 10, 2)->nullable()->default(0);
            $table->decimal('sub_qty', 10, 2)->nullable()->default(0);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('stock_qty', 10, 2)->nullable()->default(0);
            $table->decimal('rtn_main', 10, 2)->default(0.00);
            $table->decimal('rtn_sub', 10, 2)->default(0.00);
            $table->decimal('rtn_total', 10, 2)->default(0.00);
            $table->decimal('actual_main', 10, 2)->default(0.00);
            $table->decimal('actual_sub', 10, 2)->default(0.00);
            $table->decimal('actual_total', 10, 2)->default(0.00);
            $table->string('is_return')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
