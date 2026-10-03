<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warranty_claims', function (Blueprint $header) {
            $header->id();
            $header->unsignedBigInteger('branch_id');
            $header->unsignedBigInteger('customer_id')->nullable();
            $header->unsignedBigInteger('invoice_id')->nullable();
            $header->string('claim_no')->unique();
            $header->string('serial_no');
            $header->unsignedBigInteger('product_id')->nullable();
            $header->string('product_name');
            $header->string('received_condition')->nullable();
            $header->text('checking_note')->nullable();
            $header->string('place_for')->nullable();
            $header->string('status')->default('Pending');
            $header->date('received_date');
            $header->unsignedBigInteger('created_by');
            $header->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warranty_claims');
    }
};
