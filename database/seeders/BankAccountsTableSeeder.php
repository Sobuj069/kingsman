<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAccountsTableSeeder extends Seeder
{
    public function run()
    {
// // DB::table('bank_accounts')->delete();
        DB::table('bank_accounts')->insertOrIgnore(array(
            0 =>
            array(
                'id' => 1,
                'bank_name' => 'Cash',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
        ));
    }
}
