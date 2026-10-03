<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->integer('warranty_value')->nullable()->after('imei');
            $table->string('warranty_unit')->nullable()->after('warranty_value');
        });
    }

    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['warranty_value', 'warranty_unit']);
        });
    }
};
