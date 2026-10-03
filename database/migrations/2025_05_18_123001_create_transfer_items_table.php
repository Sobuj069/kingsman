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
        Schema::create('transfer_items', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('transfer_id')->nullable();
            $table->foreignId('from_branch_id')->nullable();
            $table->foreignId('to_branch_id')->nullable();
            $table->foreignId('product_id')->nullable();
            $table->foreignId('product_variation_id')->nullable();
            $table->decimal('main_qty', 10, 2)->default(0)->nullable();
            $table->decimal('sub_qty', 10, 2)->default(0)->nullable();
            $table->decimal('total_qty', 10, 2)->default(0)->nullable();
            $table->decimal('rate', 10, 2)->default(0)->nullable();
            $table->decimal('sub_total', 10, 2)->default(0)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_items');
    }
};
