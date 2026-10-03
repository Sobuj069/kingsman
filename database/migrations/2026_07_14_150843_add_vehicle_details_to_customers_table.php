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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('vehicle_name')->nullable();
            $table->string('reg_no')->nullable();
            $table->string('model')->nullable();
            $table->string('made_in')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->string('milage')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_name',
                'reg_no',
                'model',
                'made_in',
                'engine_no',
                'chassis_no',
                'milage',
                'driver_name',
                'driver_phone'
            ]);
        });
    }
};
