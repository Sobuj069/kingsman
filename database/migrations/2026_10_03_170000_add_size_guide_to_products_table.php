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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'has_size_guide')) {
                $table->tinyInteger('has_size_guide')->default(0)->after('description');
            }
            if (!Schema::hasColumn('products', 'size_guide_type')) {
                $table->string('size_guide_type')->nullable()->after('has_size_guide');
            }
            if (!Schema::hasColumn('products', 'size_guide_image')) {
                $table->string('size_guide_image')->nullable()->after('size_guide_type');
            }
            if (!Schema::hasColumn('products', 'size_guide_content')) {
                $table->text('size_guide_content')->nullable()->after('size_guide_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = ['has_size_guide', 'size_guide_type', 'size_guide_image', 'size_guide_content'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
