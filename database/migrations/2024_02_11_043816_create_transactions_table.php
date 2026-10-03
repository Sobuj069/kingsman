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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type');
            $table->date('date')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('bank_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable();
            $table->foreignId('owner_id')->nullable();
            $table->foreignId('invoice_id')->nullable();
            $table->foreignId('transfer_id')->nullable();
            $table->foreignId('purchase_id')->nullable();
            $table->foreignId('used_purchase_id')->nullable();
            $table->foreignId('used_invoice_id')->nullable();
            $table->foreignId('return_id')->nullable();
            $table->foreignId('return_pur_id')->nullable();
            $table->foreignId('actual_pay_id')->nullable()->constrained('actual_payments')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('supplier_id')->nullable();
            $table->decimal('debit', 10, 2)->nullable();
            $table->decimal('credit', 10, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
