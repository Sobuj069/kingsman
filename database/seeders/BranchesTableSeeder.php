<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branches = [
            [
                'id' => 1,
                'name' => 'Admin Branch',
                'shop_name' => 'Head Office',
                'address' => 'Mirpur 10, Dhaka - 1216, Bangladesh',
                'phone' => '01987258406',
                'email' => 'robebd.g@gmail.com',
                'status' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Robe Mirpur 2',
                'shop_name' => 'Mirpur 2 Showroom',
                'address' => 'Shop #115, 1st Floor, Mirpur 2 Shopping Complex, Dhaka',
                'phone' => '01987258406',
                'email' => 'mirpur@robe.com.bd',
                'status' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Robe Paltan',
                'shop_name' => 'Polwel Carnation Showroom',
                'address' => 'Shop #21-22, 1st Floor, Polwel Carnation, VIP Road, Dhaka',
                'phone' => '01987258405',
                'email' => 'paltan@robe.com.bd',
                'status' => 1,
            ],
            [
                'id' => 4,
                'name' => 'Robe Bashundhara City - 01',
                'shop_name' => 'Bashundhara City Level 3',
                'address' => 'Shop #41, Block B, Level 3, Panthapath, Dhaka',
                'phone' => '01987258406',
                'email' => 'bashundhara@robe.com.bd',
                'status' => 1,
            ],
            [
                'id' => 5,
                'name' => 'ROBE Chittagong',
                'shop_name' => 'Sanmar Ocean City Showroom',
                'address' => 'Sanmar Ocean City, GEC Circle, Chattogram',
                'phone' => '01987258406',
                'email' => 'ctg@robe.com.bd',
                'status' => 1,
            ],
            [
                'id' => 6,
                'name' => 'Haya By Robe Jamuna Future Park',
                'shop_name' => 'Jamuna Future Park Level 2',
                'address' => 'Shop #2C-010, Block C, Level 2, Dhaka',
                'phone' => '01987258405',
                'email' => 'jfp@robe.com.bd',
                'status' => 1,
            ],
            [
                'id' => 7,
                'name' => 'ROBE Gazipur Branch',
                'shop_name' => 'Gazipur Showroom',
                'address' => 'Joydebpur Chowrasta, Gazipur',
                'phone' => '01987258406',
                'email' => 'gazipur@robe.com.bd',
                'status' => 1,
            ],
        ];

        foreach ($branches as $item) {
            Branch::withoutGlobalScopes()->updateOrCreate(
                ['id' => $item['id']],
                [
                    'name' => $item['name'],
                    'shop_name' => $item['shop_name'],
                    'address' => $item['address'],
                    'phone' => $item['phone'],
                    'email' => $item['email'],
                    'status' => $item['status'],
                    'created_by' => 1,
                ]
            );
        }
    }
}