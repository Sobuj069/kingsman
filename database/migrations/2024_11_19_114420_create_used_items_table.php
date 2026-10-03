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
        Schema::create('used_items', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('used_id')->nullable();
            $table->foreignId('product_id')->nullable();
            $table->bigInteger('main_qty')->nullable()->default(0);
            $table->bigInteger('sub_qty')->nullable()->default(0);
            $table->decimal('rate', 10, 2)->nullable()->default(0.00);
            $table->decimal('subtotal', 10, 2)->nullable()->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_items');
    }
};
