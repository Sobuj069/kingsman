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
        Schema::create('employee_payments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('month');
            $table->date('payment_date')->nullable();
            $table->string('payment_type')->nullable();
            $table->foreignId('bank_id')->nullable();
            $table->decimal('payment', 10, 2)->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('note')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_payments');
    }
};
