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
        Schema::create('service_receives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('service_no')->unique();
            $table->string('cname')->nullable();
            $table->string('cphone')->nullable();
            $table->text('caddress')->nullable();
            $table->string('pname')->nullable();
            $table->string('pmodel')->nullable();
            $table->text('pdescription')->nullable();
            $table->date('received_date')->nullable();
            $table->date('deli_date')->nullable();
            $table->string('status')->default('Pending');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_receives');
    }
};
