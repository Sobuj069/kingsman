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
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'actual_main')) {
                $table->decimal('actual_main', 10, 2)->default(0.00)->after('rtn_total');
            }
            if (!Schema::hasColumn('invoice_items', 'actual_sub')) {
                $table->decimal('actual_sub', 10, 2)->default(0.00)->after('actual_main');
            }
            if (!Schema::hasColumn('invoice_items', 'actual_total')) {
                $table->decimal('actual_total', 10, 2)->default(0.00)->after('actual_sub');
            }
            
            if (!Schema::hasColumn('invoice_items', 'imei')) {
                $table->text('imei')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('imei');
        });
    }
};
