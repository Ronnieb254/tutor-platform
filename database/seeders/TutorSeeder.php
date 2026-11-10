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
            [
                'name'     => 'Dr. Amara Kimani',
                'email'    => 'amara@mwalimu.co.ke',
                'rate'     => 3000,
                'exp'      => 8,
                'subjects' => ['Mathematics Alternative A', 'Physics', 'Core Mathematics'],
                'city'     => 'Nairobi',
                'bio'      => 'TSC-registered Mathematics & Physics teacher with 8 years experience. National KCSE marker for Mathematics. I specialise in breaking down complex concepts clearly and building exam confidence.',
            ],
            [
                'name'     => 'Prof. James Mwangi',
                'email'    => 'james@mwalimu.co.ke',
                'rate'     => 2800,
                'exp'      => 12,
                'subjects' => ['Chemistry', 'Biology', 'General Science'],
                'city'     => 'Nairobi',
                'bio'      => 'Senior Chemistry teacher and KNEC national examiner for 12 years. Expert in KCSE marking schemes and examiner expectations. I teach you how to answer, not just what to study.',
            ],
            [
                'name'     => 'Ms. Fatuma Abdi',
                'email'    => 'fatuma@mwalimu.co.ke',
                'rate'     => 2500,
                'exp'      => 7,
                'subjects' => ['English', 'Literature in English', 'Kiswahili'],
                'city'     => 'Mombasa',
                'bio'      => 'TSC-registered English and Literature teacher. KCSE English national marker. I help students master essay writing, comprehension and oral skills that examiners reward.',
            ],
            [
                'name'     => 'Mr. David Otieno',
                'email'    => 'david@mwalimu.co.ke',
                'rate'     => 2600,
                'exp'      => 9,
                'subjects' => ['History and Government', 'Geography', 'History and Citizenship'],
                'city'     => 'Kisumu',
                'bio'      => 'Humanities teacher specialising in History & Geography for both 8-4-4 and CBC curricula. I focus on structured answers and exam technique.',
            ],
            [
                'name'     => 'Ms. Grace Wanjiku',
                'email'    => 'grace@mwalimu.co.ke',
                'rate'     => 3500,
                'exp'      => 10,
                'subjects' => ['Computer Studies', 'Mathematics Alternative A', 'Mathematics Alternative B'],
                'city'     => 'Nairobi',
                'bio'      => 'Computer Studies and Mathematics teacher. National KCSE Computer Studies marker. I make technology and maths accessible and practical for every learner.',
            ],
            [
                'name'     => 'Mr. Brian Kipchoge',
                'email'    => 'brian@mwalimu.co.ke',
                'rate'     => 2200,
                'exp'      => 5,
                'subjects' => ['KCSE Exam Strategy', 'Physics', 'Mathematics Alternative A'],
                'city'     => 'Eldoret',
                'bio'      => 'Physics and Maths teacher with a focus on KCSE exam strategy. I help Form 3 and 4 students close the gap between what they know and what examiners want to see.',
            ],
            [
                'name'     => 'Mrs. Sarah Njoroge',
                'email'    => 'sarah@mwalimu.co.ke',
                'rate'     => 2400,
                'exp'      => 11,
                'subjects' => ['Business Studies', 'Economics', 'KCSE Exam Strategy'],
                'city'     => 'Nairobi',
                'bio'      => 'Business Studies national marker and TSC-registered teacher. Over 11 years teaching in top Nairobi schools. Expert in KNEC marking criteria for Business Studies.',
            ],
            [
                'name'     => 'Mr. Ali Hassan',
                'email'    => 'ali@mwalimu.co.ke',
                'rate'     => 2000,
                'exp'      => 6,
                'subjects' => ['Islamic Religious Education (IRE)', 'Arabic', 'Kiswahili / KSL'],
                'city'     => 'Mombasa',
                'bio'      => 'IRE and Arabic specialist with 6 years of tutoring experience across JSS and Senior School levels. I help learners connect with the text and score maximum marks.',
            ],
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
                    'bio'              => $data['bio'],
                    'hourly_rate'      => $data['rate'],
                    'experience_years' => $data['exp'],
                    'languages'        => ['English', 'Kiswahili'],
                    'country'          => 'Kenya',
                    'city'             => $data['city'],
                    'is_approved'      => true,
                    'approved_at'      => now(),
                    'rating_avg'       => min(5.0, round(4.5 + ($i * 0.07), 1)),
                    'total_reviews'    => rand(18, 130),
                    'total_sessions'   => rand(40, 350),
                ]
            );

            // Attach subjects
            $subjectIds = Subject::whereIn('name', $data['subjects'])->pluck('id');
            $profile->subjects()->sync($subjectIds);

            // Set availability Mon–Fri
            for ($day = 1; $day <= 5; $day++) {
                $profile->availabilities()->updateOrCreate(
                    ['day_of_week' => $day],
                    ['start_time' => '08:00', 'end_time' => '18:00', 'is_active' => true]
                );
            }
            // Saturday morning
            $profile->availabilities()->updateOrCreate(
                ['day_of_week' => 6],
                ['start_time' => '09:00', 'end_time' => '13:00', 'is_active' => true]
            );

            // Wallet
            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => rand(5000, 50000), 'currency' => 'KES']
            );
        }

        $this->command->info('✅ Demo tutors seeded with Kenyan curriculum subjects.');
    }
}
