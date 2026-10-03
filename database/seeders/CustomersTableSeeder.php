<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomersTableSeeder extends Seeder
{
    /**
     * Seed only the essential Walk-in Customer.
     * Demo customers from JSON have been removed.
     *
     * @return void
     */
    public function run()
    {
        // Insert only the system Walk-in Customer
        DB::table('customers')->insertOrIgnore([
            [
                'branch_id'   => 1,
                'date'        => now()->toDateString(),
                'member_id'   => null,
                'name'        => 'Walk-in Customer',
                'email'       => null,
                'phone'       => '0000000000',
                'address'     => null,
                'birth_date'  => null,
                'due_amount'  => 0.00,
                'total_point' => 0.00,
                'status'      => '1',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
