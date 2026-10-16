<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PlatformSettingsSeeder::class,
            CategorySeeder::class,
            AdminSeeder::class,
            TutorSeeder::class,
            StudentSeeder::class,
            SampleBookingSeeder::class,
        ]);
    }
}
