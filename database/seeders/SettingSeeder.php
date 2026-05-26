<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'POS System Store'],
            ['key' => 'store_address', 'value' => '123 Main Street, City, Country'],
            ['key' => 'store_phone', 'value' => '+1 234 567 890'],
            ['key' => 'store_email', 'value' => 'info@pos.com'],
            ['key' => 'tax_rate', 'value' => '0'],
            ['key' => 'currency_symbol', 'value' => '$'],
            ['key' => 'receipt_header', 'value' => 'Thank you for shopping!'],
            ['key' => 'receipt_footer', 'value' => 'Visit again!'],
        ];
        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
