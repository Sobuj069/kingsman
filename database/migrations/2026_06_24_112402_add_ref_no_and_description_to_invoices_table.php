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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'ref_no')) {
                $table->string('ref_no')->nullable()->after('note');
            }
            if (!Schema::hasColumn('invoices', 'description')) {
                $table->text('description')->nullable()->after('ref_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'ref_no')) {
                $table->dropColumn('ref_no');
            }
            if (Schema::hasColumn('invoices', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
