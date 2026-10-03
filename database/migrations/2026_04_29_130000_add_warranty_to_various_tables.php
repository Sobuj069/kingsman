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
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'warranty_value')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('warranty_value')->nullable()->after('has_warranty');
                $table->string('warranty_unit')->nullable()->after('warranty_value');
            });
        }

        if (Schema::hasTable('purchase_items') && !Schema::hasColumn('purchase_items', 'warranty_value')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->integer('warranty_value')->nullable()->after('subtotal');
                $table->string('warranty_unit')->nullable()->after('warranty_value');
            });
        }

        if (Schema::hasTable('serial_numbers') && !Schema::hasColumn('serial_numbers', 'warranty_value')) {
            Schema::table('serial_numbers', function (Blueprint $table) {
                $table->integer('warranty_value')->nullable()->after('serial');
                $table->string('warranty_unit')->nullable()->after('warranty_value');
            });
        }

        if (Schema::hasTable('invoice_items') && !Schema::hasColumn('invoice_items', 'warranty_value')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->integer('warranty_value')->nullable()->after('subtotal');
                $table->string('warranty_unit')->nullable()->after('warranty_value');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['warranty_value', 'warranty_unit']);
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['warranty_value', 'warranty_unit']);
        });

        Schema::table('serial_numbers', function (Blueprint $table) {
            $table->dropColumn(['warranty_value', 'warranty_unit']);
        });
    }
};
