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
        Schema::create('installment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_id')->constrained('installments')->onDelete('cascade');
            $table->integer('installment_no');
            $table->date('due_date');
            $table->double('amount', 15, 2);
            $table->double('paid_amount', 15, 2)->default(0.00);
            $table->date('paid_date')->nullable();
            $table->string('status')->default('pending'); // pending, paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_schedules');
    }
};
