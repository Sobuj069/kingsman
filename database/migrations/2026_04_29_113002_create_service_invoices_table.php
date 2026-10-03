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
        Schema::create('service_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('service_receive_id')->nullable()->constrained('service_receives')->onDelete('set null');
            $table->string('invoice_no')->unique();
            $table->double('total_amount', 20, 2)->default(0);
            $table->double('discount', 20, 2)->default(0);
            $table->double('vat', 20, 2)->default(0);
            $table->double('net_amount', 20, 2)->default(0);
            $table->double('paid_amount', 20, 2)->default(0);
            $table->double('due_amount', 20, 2)->default(0);
            $table->date('date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_invoices');
    }
};
