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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->double('advance_pay', 15, 2)->default(0.00);
            $table->double('remaining_amount', 15, 2)->default(0.00);
            $table->integer('total_installments')->default(1);
            $table->integer('interval_days')->default(30);
            $table->double('interest_percentage', 8, 2)->default(0.00);
            $table->double('interest_amount', 15, 2)->default(0.00);
            $table->double('per_installment_amount', 15, 2)->default(0.00);
            $table->double('total_with_interest', 15, 2)->default(0.00);
            $table->date('first_due_date');
            $table->date('last_due_date');
            $table->string('status')->default('pending'); // pending, completed
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
