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
        Schema::create('stock_audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_no')->unique();
            $table->date('date');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('audited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('total_items')->default(0);
            $table->integer('matched_items')->default(0);
            $table->integer('discrepancy_items')->default(0);
            $table->decimal('total_deficit_qty', 10, 2)->default(0);
            $table->decimal('total_surplus_qty', 10, 2)->default(0);
            $table->text('note')->nullable();
            $table->string('status')->default('completed'); // draft, completed
            $table->timestamps();
        });

        Schema::create('stock_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_audit_id')->constrained('stock_audits')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variation_id')->nullable();
            $table->decimal('system_qty', 10, 2)->default(0);
            $table->decimal('scanned_qty', 10, 2)->default(0);
            $table->decimal('physical_qty', 10, 2)->default(0);
            $table->decimal('diff_qty', 10, 2)->default(0); // physical_qty - system_qty
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_audit_items');
        Schema::dropIfExists('stock_audits');
    }
};
