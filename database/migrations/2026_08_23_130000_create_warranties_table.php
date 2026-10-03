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
        if (!Schema::hasTable('warranties')) {
            Schema::create('warranties', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('duration')->nullable();
                $table->string('period')->default('Month'); // Day, Month, Year, Lifetime
                $table->text('description')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'warranty_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('warranty_id')->nullable()->after('has_warranty');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'warranty_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('warranty_id');
            });
        }
    }
};
