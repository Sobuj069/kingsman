<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{

    public function run()
    {

// // DB::table('branches')->delete();
        DB::table('branches')->insertOrIgnore(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Admin Branch',
                'phone' => '01234567890',
                'address' => 'All Address',
                'status' => 1,
                'created_by' => 1,
                'created_at' => '2023-11-15 15:47:05',
                'updated_at' => '2023-11-15 15:47:05',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Default Branch',
                'phone' => '01234567890',
                'address' => 'Default Address',
                'status' => 1,
                'created_by' => 1,
                'created_at' => '2023-11-15 15:47:05',
                'updated_at' => '2023-11-15 15:47:05',
            ),
        ));


// // DB::table('users')->delete();
        DB::table('users')->insertOrIgnore(array(
            0 =>
            array(
                'id' => 1,
                'role_id' => 1,
                'name' => 'Admin',
                'branch_id' => 1,
                'email' => 'admin@fastit.com',
                'phone' => NULL,
                'address' => NULL,
                'email_verified_at' => NULL,
               'password' => Hash::make('12345678'),
                'two_factor_secret' => NULL,
                'two_factor_recovery_codes' => NULL,
                'two_factor_confirmed_at' => NULL,
                'image' => NULL,
                'remember_token' => NULL,
                'status' => 1,
                'last_login' => NULL,
                'created_at' => '2023-11-15 15:47:05',
                'updated_at' => '2023-11-15 15:47:05',
            ),
            1 =>
            array(
                'id' => 2,
                'role_id' => 2,
                'name' => 'Super Admin',
                'branch_id' => 1,
                'email' => 'superadmin@fastit.com',
                'phone' => NULL,
                'address' => NULL,
                'email_verified_at' => NULL,
                'password' => Hash::make('fast@superadmin'),
                'two_factor_secret' => NULL,
                'two_factor_recovery_codes' => NULL,
                'two_factor_confirmed_at' => NULL,
                'image' => NULL,
                'remember_token' => NULL,
                'status' => 1,
                'last_login' => NULL,
                'created_at' => '2023-11-15 15:47:05',
                'updated_at' => '2023-11-15 15:47:05',
            ),
            2 =>
            array(
                'id' => 3,
                'role_id' => 3,
                'name' => 'Salesman',
                'branch_id' => 1,
                'email' => 'salesman@fastit.com',
                'phone' => NULL,
                'address' => NULL,
                'email_verified_at' => NULL,
                'password' => Hash::make('12345678'),
                'two_factor_secret' => NULL,
                'two_factor_recovery_codes' => NULL,
                'two_factor_confirmed_at' => NULL,
                'image' => NULL,
                'remember_token' => NULL,
                'status' => 1,
                'last_login' => NULL,
                'created_at' => '2023-11-15 15:47:05',
                'updated_at' => '2023-11-15 15:47:05',
            ),
        ));


// // DB::table('owner_ships')->delete();
        DB::table('owner_ships')->insertOrIgnore(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Default Woner',
                'date' => '2025-05-19',
                'email' => 'default@gmail.com',
                'phone' => '000000000000',
                'address' => 'mirpur-13,Dhaka.',
                'deposit' => '0.00',
                'withdraw' => '0.00',
                'status' => '1',
                'created_at' => '2023-11-22 20:29:26',
                'updated_at' => '2023-12-01 16:57:40',
            )
        ));
        
    }
}
