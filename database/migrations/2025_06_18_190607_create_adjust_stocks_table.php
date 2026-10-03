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
        Schema::create('adjust_stocks', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('adjust_no')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('adjust_by')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('note')->nullable();
            $table->tinyInteger('stock_status')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjust_stocks');
    }
};
