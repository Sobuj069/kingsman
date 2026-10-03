<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('vehicle_reg_no')->nullable()->after('customer_id');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->string('vehicle_reg_no')->nullable()->after('customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('vehicle_reg_no');
        });
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('vehicle_reg_no');
        });
    }
};

