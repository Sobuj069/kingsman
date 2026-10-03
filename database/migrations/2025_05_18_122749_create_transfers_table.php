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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('transfer_no')->nullable();
            $table->foreignId('from_branch_id')->nullable();
            $table->foreignId('to_branch_id')->nullable();
            $table->foreignId('transfer_by')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->foreignId('transfer_receive_by')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
