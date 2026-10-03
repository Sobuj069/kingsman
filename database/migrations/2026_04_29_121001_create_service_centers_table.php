<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_centers', function (Blueprint $header) {
            $header->id();
            $header->string('name');
            $header->string('mobile')->nullable();
            $header->string('email')->nullable();
            $header->text('address')->nullable();
            $header->tinyInteger('status')->default(1);
            $header->unsignedBigInteger('created_by');
            $header->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_centers');
    }
};
