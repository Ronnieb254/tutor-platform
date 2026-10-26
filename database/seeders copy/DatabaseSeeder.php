<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KenyanCurriculumSeeder::class,  // Seed Kenyan curriculum subjects
            PlatformSettingsSeeder::class,
            CategorySeeder::class,
            AdminSeeder::class,
            TutorSeeder::class,
            StudentSeeder::class,
            SampleBookingSeeder::class,
        ]);
    }
}
