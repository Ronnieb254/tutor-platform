<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Mathematics',  'icon' => '📐', 'subjects' => ['Algebra', 'Calculus', 'Statistics', 'Geometry', 'Number Theory']],
            ['name' => 'Sciences',     'icon' => '🔬', 'subjects' => ['Biology', 'Chemistry', 'Physics', 'Environmental Science']],
            ['name' => 'Languages',    'icon' => '🌍', 'subjects' => ['English', 'Swahili', 'French', 'Arabic', 'German']],
            ['name' => 'Programming',  'icon' => '💻', 'subjects' => ['Python', 'JavaScript', 'Web Development', 'Data Science', 'Mobile Apps']],
            ['name' => 'Business',     'icon' => '📊', 'subjects' => ['Accounting', 'Economics', 'Business Studies', 'Entrepreneurship']],
            ['name' => 'Arts & Music', 'icon' => '🎨', 'subjects' => ['Fine Art', 'Music Theory', 'Guitar', 'Piano', 'Drawing']],
            ['name' => 'Humanities',   'icon' => '📚', 'subjects' => ['History', 'Geography', 'Religious Studies', 'Philosophy']],
            ['name' => 'Test Prep',    'icon' => '📝', 'subjects' => ['KCSE Prep', 'SAT', 'IELTS', 'TOEFL', 'University Entrance']],
        ];

        foreach ($categories as $i => $data) {
            $category = Category::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['name'])],
                [
                    'name'       => $data['name'],
                    'icon'       => $data['icon'],
                    'is_active'  => true,
                    'sort_order' => $i,
                ]
            );

            foreach ($data['subjects'] as $subjectName) {
                Subject::updateOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($subjectName)],
                    [
                        'category_id' => $category->id,
                        'name'        => $subjectName,
                        'is_active'   => true,
                    ]
                );
            }
        }
    }
}
