<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessSetting;

class BusinessSettingsTableSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['id' => 1, 'type' => 'system_icon', 'value' => 'robe_logo.png'],
            ['id' => 2, 'type' => 'system_logo', 'value' => 'robe_logo.png'],
            ['id' => 3, 'type' => 'pro_barcode', 'value' => 'single'],
            ['id' => 4, 'type' => 'inv_logo', 'value' => 'robe_logo.png'],
            ['id' => 5, 'type' => 'inv_design', 'value' => ''],
            ['id' => 6, 'type' => 'com_name', 'value' => 'ROBE'],
            ['id' => 7, 'type' => 'com_email', 'value' => 'robebd.g@gmail.com'],
            ['id' => 8, 'type' => 'com_phone', 'value' => '01987258406'],
            ['id' => 9, 'type' => 'com_address', 'value' => 'Mirpur 10, Dhaka - 1216, Bangladesh'],
            ['id' => 10, 'type' => 'com_currency', 'value' => 'BDT'],
        ];

        foreach ($settings as $setting) {
            BusinessSetting::updateOrCreate(
                ['type' => $setting['type']],
                [
                    'id' => $setting['id'],
                    'value' => $setting['value'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
