<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warranty_deliveries', function (Blueprint $header) {
            $header->id();
            $header->unsignedBigInteger('warranty_claim_id');
            $header->unsignedBigInteger('delivered_product_id');
            $header->string('delivered_serial');
            $header->text('delivery_note')->nullable();
            $header->date('delivered_date');
            $header->unsignedBigInteger('created_by');
            $header->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warranty_deliveries');
    }
};
