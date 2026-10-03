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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trans_type');
            $table->date('date')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('bank_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable();
            $table->foreignId('owner_id')->nullable();
            $table->foreignId('from_bank_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('to_bank_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->foreignId('return_id')->nullable();
            $table->foreignId('return_pur_id')->nullable();
            $table->foreignId('used_purchase_id')->nullable();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('cascade');
            $table->foreignId('actual_pay_id')->nullable()->constrained('actual_payments')->onDelete('cascade');
            $table->double('amount')->default(0.00);
            $table->string('pay_type')->nullable();
            $table->string('note')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
