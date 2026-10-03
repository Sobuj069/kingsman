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
        Schema::create('useds', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('useds');
    }
};
