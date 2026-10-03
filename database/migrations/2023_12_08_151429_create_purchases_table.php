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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('purchase_no')->nullable();
            $table->foreignId('transfer_id')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            $table->decimal('estimated_amount', 10, 2)->default(0.00);
            $table->string('discount')->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('total_paid', 10, 2)->default(0.00);
            $table->decimal('total_due', 10, 2)->default(0.00);
            $table->decimal('rtn_total_amount', 10, 2)->default(0.00);
            $table->decimal('rtn_total_paid', 10, 2)->default(0.00);
            $table->decimal('rtn_total_due', 10, 2)->default(0.00);
            $table->decimal('return_amount', 10, 2)->default(0.00)->nullable();
            $table->string('supplier_invoice_no')->nullable();
            $table->string('supplier_invoice_date')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('is_transfer')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
