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
        Schema::create('actual_payments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('branch_id');
            $table->string('account_type');
            $table->string('wallet_type');
            $table->string('pay_type');
            $table->decimal('amount', 10, 2);
            $table->string('discount_amount',)->nullable()->default('0.00');
            $table->string('note')->nullable();
            $table->string('supplier_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actual_payments');
    }
};
