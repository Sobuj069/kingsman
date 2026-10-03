<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Seed only the essential Walk-in Supplier.
     * Demo suppliers from JSON have been removed.
     *
     * @return void
     */
    public function run()
    {
        // Insert only the system Walk-in Supplier
        DB::table('suppliers')->insertOrIgnore([
            [
                'branch_id'      => 1,
                'date'           => now()->toDateString(),
                'name'           => 'Walk-in Supplier',
                'email'          => null,
                'phone'          => '0000000000',
                'address'        => null,
                'advance_amount' => 0.00,
                'due_amount'     => 0.00,
                'status'         => '1',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}
