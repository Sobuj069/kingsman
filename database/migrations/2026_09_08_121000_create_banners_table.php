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
        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('position')->default('hero'); // hero, promo_dual, promo_festive, category, general
                $table->string('image');
                $table->string('link')->nullable();
                $table->integer('order')->default(0);
                $table->tinyInteger('status')->default(1); // 1 = Active, 0 = Inactive
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
