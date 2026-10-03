<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


// // DB::table('units')->delete();
        DB::table('units')->insertOrIgnore(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'pcs',
                'related_unit_id' => NULL,
                'related_sign' => '*',
                'related_value' => 1,
                'status' => 1,
                'created_at' => '2023-12-08 18:47:03',
                'updated_at' => '2023-12-08 18:47:03',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'gm',
                'related_unit_id' => 5,
                'related_sign' => '*',
                'related_value' => 1000,
                'status' => 1,
                'created_at' => '2023-12-08 18:43:36',
                'updated_at' => '2023-12-08 18:46:44',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'kg',
                'related_unit_id' => 2,
                'related_sign' => '*',
                'related_value' => 1000,
                'status' => 1,
                'created_at' => '2023-12-08 18:43:51',
                'updated_at' => '2023-12-08 18:43:51',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'mg',
                'related_unit_id' => NULL,
                'related_sign' => '*',
                'related_value' => 1,
                'status' => 1,
                'created_at' => '2023-12-08 18:45:31',
                'updated_at' => '2023-12-08 18:45:31',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'dz',
                'related_unit_id' => 1,
                'related_sign' => '*',
                'related_value' => 12,
                'status' => 1,
                'created_at' => '2023-12-08 18:47:29',
                'updated_at' => '2023-12-08 18:47:29',
            ),
        ));


    }
}
