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
        if (!Schema::hasTable('child_categories')) {
            Schema::create('child_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id')->nullable()->index();
                $table->unsignedBigInteger('sub_category_id')->index();
                $table->string('name');
                $table->string('slug')->nullable()->index();
                $table->string('image')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'child_category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('child_category_id')->nullable()->after('sub_category_id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'child_category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('child_category_id');
            });
        }

        Schema::dropIfExists('child_categories');
    }
};
