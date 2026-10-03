<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_claims', function (Blueprint $header) {
            $header->id();
            $header->unsignedBigInteger('supplier_id');
            $header->string('claim_no')->unique();
            $header->date('date');
            $header->string('status')->default('Pending');
            $header->unsignedBigInteger('created_by');
            $header->timestamps();
        });

        Schema::create('supplier_claim_items', function (Blueprint $header) {
            $header->id();
            $header->unsignedBigInteger('supplier_claim_id');
            $header->unsignedBigInteger('warranty_claim_id');
            $header->unsignedBigInteger('product_id');
            $header->string('serial_no');
            $header->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_claim_items');
        Schema::dropIfExists('supplier_claims');
    }
};
