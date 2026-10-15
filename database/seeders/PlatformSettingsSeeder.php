<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'platform_name',    'value' => 'Mwalimu',   'group' => 'general'],
            ['key' => 'platform_tagline', 'value' => 'Find verified tutors in Kenya', 'group' => 'general'],
            ['key' => 'commission_rate',  'value' => '0.15',         'group' => 'payments'],
            ['key' => 'currency',         'value' => 'KES',          'group' => 'payments'],
            ['key' => 'currency_symbol',  'value' => 'KSh',          'group' => 'payments'],
            ['key' => 'min_hourly_rate',  'value' => '500',          'group' => 'payments'],
            ['key' => 'contact_email',    'value' => 'hello@Mwalimu.co.ke', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
