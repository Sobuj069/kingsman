<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('serial_numbers', 'branch_id')) {
            Schema::table('serial_numbers', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('product_id');
            });

            // Populate branch_id from purchases
            DB::statement("UPDATE serial_numbers sn JOIN purchases p ON sn.purchase_id = p.id SET sn.branch_id = p.branch_id");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serial_numbers', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
};
