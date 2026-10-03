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
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();
            $table->string('pre_order_no')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('created_by');
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'converted', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('pre_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pre_order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('pre_order_id')->references('id')->on('pre_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_order_items');
        Schema::dropIfExists('pre_orders');
    }
};
