<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'platform_name',              'value' => 'Mwalimu',                                   'group' => 'general'],
            ['key' => 'platform_tagline',           'value' => 'Instant 1-on-1 tutoring, matched in seconds', 'group' => 'general'],
            ['key' => 'contact_email',              'value' => 'info@ovaldesk.co.ke',                       'group' => 'general'],
            ['key' => 'contact_phone_1',            'value' => '0724367814',                                'group' => 'general'],
            ['key' => 'contact_phone_2',            'value' => '0723636901',                                'group' => 'general'],
            ['key' => 'contact_address',            'value' => 'Suite 11, Samima Suites, Nairobi West',     'group' => 'general'],
            ['key' => 'contact_website',            'value' => 'https://ovaldesk.co.ke',                    'group' => 'general'],
            // Fixed pricing (per Mwalimu PRD)
            ['key' => 'price_15min',                'value' => '200',                                       'group' => 'payments'],
            ['key' => 'price_30min',                'value' => '400',                                       'group' => 'payments'],
            ['key' => 'price_45min',                'value' => '600',                                       'group' => 'payments'],
            ['key' => 'price_60min',                'value' => '800',                                       'group' => 'payments'],
            ['key' => 'clinic_price',               'value' => '1000',                                      'group' => 'payments'],
            ['key' => 'speedy_verification_fee',    'value' => '1000',                                      'group' => 'payments'],
            ['key' => 'commission_rate',            'value' => '0.15',                                      'group' => 'payments'],
            ['key' => 'currency',                   'value' => 'KES',                                       'group' => 'payments'],
            ['key' => 'currency_symbol',            'value' => 'KSh',                                       'group' => 'payments'],
            // Verification timelines
            ['key' => 'standard_verification_hours','value' => '72',                                        'group' => 'verification'],
            ['key' => 'speedy_verification_hours',  'value' => '24',                                        'group' => 'verification'],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
