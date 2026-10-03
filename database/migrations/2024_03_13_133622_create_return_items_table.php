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
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->string('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('return_id')->constrained('return_tbls')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variation_id')->nullable();
            $table->decimal('rate', 10, 2)->default(0.00);
            $table->decimal('main_qty', 10, 2)->nullable()->default(0);
            $table->decimal('sub_qty', 10, 2)->nullable()->default(0);
            $table->decimal('purchase_price', 10, 2)->nullable()->default(0);
            $table->decimal('pur_subtotal', 10, 2)->nullable()->default(0);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
