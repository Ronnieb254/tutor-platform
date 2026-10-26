<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [

            // ─────────────────────────────────────────────────────────────────
            // JSS – Grade 7, 8, 9 (CBC Junior Secondary School)
            // ─────────────────────────────────────────────────────────────────
            [
                'name' => 'JSS – Grade 7, 8, 9',
                'icon' => '📗',
                'description' => 'CBC Junior Secondary School subjects for Grades 7, 8 and 9.',
                'subjects' => [
                    'English',
                    'Kiswahili / KSL',
                    'Mathematics',
                    'Integrated Science',
                    'Social Studies',
                    'Religious Education (CRE / IRE / HRE)',
                    'Agriculture',
                    'Pre-Technical Studies',
                    'Creative Arts and Sports',
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // Senior School – Core (Grade 10, 11, 12)
            // ─────────────────────────────────────────────────────────────────
            [
                'name' => 'Senior School – Core (G10–12)',
                'icon' => '📘',
                'description' => 'Compulsory core subjects for all Senior School learners.',
                'subjects' => [
                    'English',
                    'Kiswahili / KSL',
                    'Core Mathematics',
                    'Essential Mathematics',
                    'Community Service Learning (CSL)',
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // Senior School – STEM Pathway
            // ─────────────────────────────────────────────────────────────────
            [
                'name' => 'Senior School – STEM Pathway',
                'icon' => '🔬',
                'description' => 'Science, Technology, Engineering and Mathematics pathway subjects.',
                'subjects' => [
                    'Biology',
                    'Chemistry',
                    'Physics',
                    'General Science',
                    'Agriculture',
                    'Computer Studies',
                    'Home Science',
                    'Aviation',
                    'Building Construction',
                    'Electricity',
                    'Metalwork',
                    'Power Mechanics',
                    'Wood Technology',
                    'Media Technology',
                    'Marine and Fisheries Technology',
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // Senior School – Social Sciences Pathway
            // ─────────────────────────────────────────────────────────────────
            [
                'name' => 'Senior School – Social Sciences',
                'icon' => '🌍',
                'description' => 'Social Sciences and Languages pathway for Senior School.',
                'subjects' => [
                    'Literature in English',
                    'Indigenous Languages',
                    'Fasihi ya Kiswahili',
                    'Sign Language',
                    'Arabic',
                    'French',
                    'German',
                    'Mandarin Chinese',
                    'Christian Religious Education (CRE)',
                    'Islamic Religious Education (IRE)',
                    'Hindu Religious Education (HRE)',
                    'Business Studies',
                    'History and Citizenship',
                    'Geography',
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // Senior School – Arts and Sports Science Pathway
            // ─────────────────────────────────────────────────────────────────
            [
                'name' => 'Senior School – Arts & Sports',
                'icon' => '🎨',
                'description' => 'Arts and Sports Science pathway for Senior School.',
                'subjects' => [
                    'Sports and Recreation',
                    'Music and Dance',
                    'Theatre and Film',
                    'Fine Arts',
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 8-4-4 Form 3 & Form 4 (KCSE)
            // ─────────────────────────────────────────────────────────────────
            [
                'name' => 'Form 3 & 4 – KCSE (8-4-4)',
                'icon' => '📝',
                'description' => 'Traditional 8-4-4 curriculum subjects for Form 3 and Form 4 (KCSE preparation).',
                'subjects' => [
                    'English',
                    'Kiswahili',
                    'Mathematics Alternative A',
                    'Mathematics Alternative B',
                    'Biology',
                    'Physics',
                    'Chemistry',
                    'History and Government',
                    'Geography',
                    'Christian Religious Education (CRE)',
                    'Islamic Religious Education (IRE)',
                    'Hindu Religious Education (HRE)',
                    'Home Science',
                    'Art and Design',
                    'Agriculture',
                    'Woodwork',
                    'Metalwork',
                    'Building Construction',
                    'Power Mechanics',
                    'Electricity',
                    'Drawing and Design',
                    'Aviation Technology',
                    'Computer Studies',
                    'French',
                    'German',
                    'Arabic',
                    'Kenyan Sign Language',
                    'Music',
                    'Business Studies',
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // Exam Preparation (cross-curriculum)
            // ─────────────────────────────────────────────────────────────────
            [
                'name' => 'Exam Preparation',
                'icon' => '🏆',
                'description' => 'KCSE, KNEC and national exam preparation sessions with expert tutors.',
                'subjects' => [
                    'KCSE Exam Strategy',
                    'KNEC Past Papers – Mathematics',
                    'KNEC Past Papers – English',
                    'KNEC Past Papers – Sciences',
                    'Marking Scheme Analysis',
                    'Examiner Clinic Sessions',
                    'Time Management in Exams',
                    'Essay and Composition Writing',
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // Primary – Class 1 to 6 (CBC Lower Primary / Upper Primary)
            // ─────────────────────────────────────────────────────────────────
            [
                'name' => 'Primary – Class 1 to 6',
                'icon' => '✏️',
                'description' => 'CBC Lower and Upper Primary learning areas for Classes 1 through 6.',
                'subjects' => [
                    'English Activities',
                    'Kiswahili Activities',
                    'Mathematical Activities',
                    'Environmental Activities',
                    'Creative Arts',
                    'Music',
                    'Physical and Health Education',
                    'Religious Education',
                    'Life Skills',
                ],
            ],
        ];

        $sortOrder = 0;
        foreach ($curriculum as $group) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($group['name'])],
                [
                    'name'        => $group['name'],
                    'icon'        => $group['icon'],
                    'description' => $group['description'],
                    'is_active'   => true,
                    'sort_order'  => $sortOrder++,
                ]
            );

            foreach ($group['subjects'] as $subjectName) {
                Subject::updateOrCreate(
                    ['slug' => Str::slug($subjectName)],
                    [
                        'category_id' => $category->id,
                        'name'        => $subjectName,
                        'is_active'   => true,
                    ]
                );
            }
        }

        $this->command->info('✅ Kenyan curriculum categories and subjects seeded.');
    }
}
