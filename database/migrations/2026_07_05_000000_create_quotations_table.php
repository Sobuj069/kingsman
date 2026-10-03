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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_no')->unique();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('customer_id');
            $table->date('date');
            $table->decimal('estimated_amount', 15, 2)->default(0.00);
            $table->string('discount')->default('0.00');
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->string('vat')->default('0.00');
            $table->decimal('vat_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->decimal('delivery_charge', 15, 2)->default(0.00);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variation_id')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->decimal('rate', 15, 2);
            $table->decimal('main_qty', 15, 2);
            $table->decimal('sub_qty', 15, 2)->default(0.00);
            $table->decimal('product_discount', 15, 2)->default(0.00);
            $table->decimal('subtotal', 15, 2);
            $table->string('product_unit')->nullable();
            $table->text('imei')->nullable();
            $table->string('warranty_value')->nullable();
            $table->string('warranty_unit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
