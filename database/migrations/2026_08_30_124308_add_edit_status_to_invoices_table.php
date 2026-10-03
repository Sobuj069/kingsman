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
            if (!Schema::hasColumn('invoices', 'is_edited')) {
                $table->tinyInteger('is_edited')->default(0)->after('status')->comment('0: Normal, 1: Edited, 2: Exchange');
            }
            if (!Schema::hasColumn('invoices', 'edit_status')) {
                $table->string('edit_status')->nullable()->after('is_edited')->comment('edited, exchange');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'is_edited')) {
                $table->dropColumn('is_edited');
            }
            if (Schema::hasColumn('invoices', 'edit_status')) {
                $table->dropColumn('edit_status');
            }
        });
    }
};
