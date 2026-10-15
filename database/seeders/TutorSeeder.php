<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TutorProfile;
use App\Models\Subject;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TutorSeeder extends Seeder
{
    public function run(): void
    {
        $tutors = [
            ['name' => 'Dr. Amara Kimani',   'email' => 'amara@Mwalimu.co.ke',  'rate' => 3000, 'exp' => 8,  'subjects' => ['Algebra','Calculus','Statistics'],      'city' => 'Nairobi'],
            ['name' => 'Prof. James Mwangi',  'email' => 'james@Mwalimu.co.ke',  'rate' => 2500, 'exp' => 12, 'subjects' => ['Physics','Mathematics'],                 'city' => 'Nairobi'],
            ['name' => 'Ms. Fatuma Abdi',     'email' => 'fatuma@Mwalimu.co.ke', 'rate' => 2000, 'exp' => 5,  'subjects' => ['English','Swahili','French'],            'city' => 'Mombasa'],
            ['name' => 'Mr. David Otieno',    'email' => 'david@Mwalimu.co.ke',  'rate' => 2800, 'exp' => 7,  'subjects' => ['Chemistry','Biology'],                   'city' => 'Kisumu'],
            ['name' => 'Ms. Grace Wanjiku',   'email' => 'grace@Mwalimu.co.ke',  'rate' => 3500, 'exp' => 10, 'subjects' => ['Python','Web Development','JavaScript'], 'city' => 'Nairobi'],
            ['name' => 'Mr. Brian Kipchoge',  'email' => 'brian@Mwalimu.co.ke',  'rate' => 1800, 'exp' => 4,  'subjects' => ['KCSE Prep','Mathematics','Physics'],     'city' => 'Eldoret'],
        ];

        foreach ($tutors as $i => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'role'              => 'tutor',
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]
            );

            $profile = TutorProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bio'              => "Experienced tutor with {$data['exp']} years of teaching. Passionate about helping students achieve their potential. Based in {$data['city']}.",
                    'hourly_rate'      => $data['rate'],
                    'experience_years' => $data['exp'],
                    'languages'        => ['English', 'Swahili'],
                    'country'          => 'Kenya',
                    'city'             => $data['city'],
                    'is_approved'      => true,
                    'approved_at'      => now(),
                    'rating_avg'       => round(4.5 + ($i * 0.08), 1),
                    'total_reviews'    => rand(15, 120),
                    'total_sessions'   => rand(40, 300),
                ]
            );

            // Sync subjects
            $subjectIds = Subject::whereIn('name', $data['subjects'])->pluck('id');
            $profile->subjects()->sync($subjectIds);

            // Set availability (Mon–Fri)
            for ($day = 1; $day <= 5; $day++) {
                $profile->availabilities()->updateOrCreate(
                    ['day_of_week' => $day],
                    ['start_time' => '08:00', 'end_time' => '18:00', 'is_active' => true]
                );
            }

            // Create wallet
            Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => rand(5000, 50000), 'currency' => 'KES']);
        }
    }
}
