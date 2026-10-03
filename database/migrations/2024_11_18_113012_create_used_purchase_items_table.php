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
        Schema::create('used_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('rate', 10, 2)->default(0.00);
            $table->decimal('main_qty', 10, 2)->nullable()->default(0);
            $table->decimal('sub_qty', 10, 2)->nullable()->default(0);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_purchase_items');
    }
};
