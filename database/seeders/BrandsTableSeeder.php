<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    /**
     * Seed only the essential default brand.
     * Demo brands from JSON have been removed.
     *
     * @return void
     */
    public function run()
    {
        // Insert only the default system brand
        $brandId = DB::table('brands')->insertGetId([
            'name'       => 'Default Brand',
            'slug'       => 'default-brand',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // No branch assignment needed - brands are visible to all branches by default
    }
}
