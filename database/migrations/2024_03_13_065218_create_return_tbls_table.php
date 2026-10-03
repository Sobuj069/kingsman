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
        Schema::create('return_tbls', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->string('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('customer_id')->constrained('customers')->onDelete('cascade');
            $table->decimal('estimated_amount', 10, 2)->default(0.00);
            $table->string('discount', 10)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_return', 10, 2)->default(0.00);
            $table->tinyInteger('status')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_tbls');
    }
};
