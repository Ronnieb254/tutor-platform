<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KenyanCurriculumSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('subjects')->delete();
        DB::table('categories')->delete();

        $categories = [
            // 8-4-4 System
            [
                'name' => '8-4-4 System (Form 1-4)',
                'slug' => '8-4-4-system',
                'icon' => '📚',
                'description' => 'Kenya Certificate of Secondary Education (KCSE) subjects for Form 1-4',
                'is_active' => true,
                'sort_order' => 1,
            ],
            
            // CBC Junior Secondary
            [
                'name' => 'CBC Junior Secondary (Grade 7-9)',
                'slug' => 'cbc-junior-secondary',
                'icon' => '🎓',
                'description' => 'Competency-Based Curriculum for Junior Secondary School (Grade 7, 8, 9)',
                'is_active' => true,
                'sort_order' => 2,
            ],
            
            // CBC Senior School - Core
            [
                'name' => 'CBC Senior School Core (Grade 10-12)',
                'slug' => 'cbc-senior-core',
                'icon' => '⭐',
                'description' => 'Core subjects for all Senior School students',
                'is_active' => true,
                'sort_order' => 3,
            ],
            
            // CBC STEM Pathway
            [
                'name' => 'CBC STEM Pathway (Grade 10-12)',
                'slug' => 'cbc-stem-pathway',
                'icon' => '🔬',
                'description' => 'Science, Technology, Engineering and Mathematics pathway subjects',
                'is_active' => true,
                'sort_order' => 4,
            ],
            
            // CBC Social Sciences Pathway
            [
                'name' => 'CBC Social Sciences (Grade 10-12)',
                'slug' => 'cbc-social-sciences',
                'icon' => '🌍',
                'description' => 'Social Sciences pathway subjects including languages and humanities',
                'is_active' => true,
                'sort_order' => 5,
            ],
            
            // CBC Arts and Sports Pathway
            [
                'name' => 'CBC Arts & Sports (Grade 10-12)',
                'slug' => 'cbc-arts-sports',
                'icon' => '🎨',
                'description' => 'Arts and Sports Science pathway subjects',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Get category IDs
        $kcse = DB::table('categories')->where('slug', '8-4-4-system')->first()->id;
        $jss = DB::table('categories')->where('slug', 'cbc-junior-secondary')->first()->id;
        $seniorCore = DB::table('categories')->where('slug', 'cbc-senior-core')->first()->id;
        $stem = DB::table('categories')->where('slug', 'cbc-stem-pathway')->first()->id;
        $social = DB::table('categories')->where('slug', 'cbc-social-sciences')->first()->id;
        $arts = DB::table('categories')->where('slug', 'cbc-arts-sports')->first()->id;

        $subjects = [
            // ============ 8-4-4 SYSTEM (KCSE) ============
            ['category_id' => $kcse, 'name' => 'English', 'icon' => '📖'],
            ['category_id' => $kcse, 'name' => 'Kiswahili', 'icon' => '📗'],
            ['category_id' => $kcse, 'name' => 'Mathematics Alternative A', 'icon' => '🔢'],
            ['category_id' => $kcse, 'name' => 'Mathematics Alternative B', 'icon' => '📐'],
            ['category_id' => $kcse, 'name' => 'Biology', 'icon' => '🧬'],
            ['category_id' => $kcse, 'name' => 'Physics', 'icon' => '⚛️'],
            ['category_id' => $kcse, 'name' => 'Chemistry', 'icon' => '🧪'],
            ['category_id' => $kcse, 'name' => 'History and Government', 'icon' => '🏛️'],
            ['category_id' => $kcse, 'name' => 'Geography', 'icon' => '🗺️'],
            ['category_id' => $kcse, 'name' => 'Christian Religious Education (CRE)', 'icon' => '✝️'],
            ['category_id' => $kcse, 'name' => 'Islamic Religious Education (IRE)', 'icon' => '☪️'],
            ['category_id' => $kcse, 'name' => 'Hindu Religious Education (HRE)', 'icon' => '🕉️'],
            ['category_id' => $kcse, 'name' => 'Home Science', 'icon' => '🏠'],
            ['category_id' => $kcse, 'name' => 'Art and Design', 'icon' => '🎨'],
            ['category_id' => $kcse, 'name' => 'Agriculture', 'icon' => '🌾'],
            ['category_id' => $kcse, 'name' => 'Woodwork', 'icon' => '🪵'],
            ['category_id' => $kcse, 'name' => 'Metalwork', 'icon' => '🔧'],
            ['category_id' => $kcse, 'name' => 'Building Construction', 'icon' => '🏗️'],
            ['category_id' => $kcse, 'name' => 'Power Mechanics', 'icon' => '⚙️'],
            ['category_id' => $kcse, 'name' => 'Electricity', 'icon' => '⚡'],
            ['category_id' => $kcse, 'name' => 'Drawing and Design', 'icon' => '📏'],
            ['category_id' => $kcse, 'name' => 'Aviation Technology', 'icon' => '✈️'],
            ['category_id' => $kcse, 'name' => 'Computer Studies', 'icon' => '💻'],
            ['category_id' => $kcse, 'name' => 'French', 'icon' => '🇫🇷'],
            ['category_id' => $kcse, 'name' => 'German', 'icon' => '🇩🇪'],
            ['category_id' => $kcse, 'name' => 'Arabic', 'icon' => '🇸🇦'],
            ['category_id' => $kcse, 'name' => 'Kenyan Sign Language', 'icon' => '🤟'],
            ['category_id' => $kcse, 'name' => 'Music', 'icon' => '🎵'],
            ['category_id' => $kcse, 'name' => 'Business Studies', 'icon' => '💼'],

            // ============ CBC JUNIOR SECONDARY (Grade 7-9) ============
            ['category_id' => $jss, 'name' => 'English', 'icon' => '📖'],
            ['category_id' => $jss, 'name' => 'Kiswahili', 'icon' => '📗'],
            ['category_id' => $jss, 'name' => 'Kenyan Sign Language (KSL)', 'icon' => '🤟'],
            ['category_id' => $jss, 'name' => 'Mathematics', 'icon' => '🔢'],
            ['category_id' => $jss, 'name' => 'Integrated Science', 'icon' => '🔬'],
            ['category_id' => $jss, 'name' => 'Social Studies', 'icon' => '🌍'],
            ['category_id' => $jss, 'name' => 'Religious Education', 'icon' => '📿'],
            ['category_id' => $jss, 'name' => 'Agriculture', 'icon' => '🌾'],
            ['category_id' => $jss, 'name' => 'Pre-Technical Studies', 'icon' => '🔧'],
            ['category_id' => $jss, 'name' => 'Creative Arts and Sports', 'icon' => '🎨'],

            // ============ CBC SENIOR SCHOOL CORE (Grade 10-12) ============
            ['category_id' => $seniorCore, 'name' => 'English', 'icon' => '📖'],
            ['category_id' => $seniorCore, 'name' => 'Kiswahili', 'icon' => '📗'],
            ['category_id' => $seniorCore, 'name' => 'Kenyan Sign Language (KSL)', 'icon' => '🤟'],
            ['category_id' => $seniorCore, 'name' => 'Core Mathematics', 'icon' => '🔢'],
            ['category_id' => $seniorCore, 'name' => 'Essential Mathematics', 'icon' => '📐'],
            ['category_id' => $seniorCore, 'name' => 'Community Service Learning (CSL)', 'icon' => '🤝'],

            // ============ CBC STEM PATHWAY (Grade 10-12) ============
            ['category_id' => $stem, 'name' => 'Biology', 'icon' => '🧬'],
            ['category_id' => $stem, 'name' => 'Chemistry', 'icon' => '🧪'],
            ['category_id' => $stem, 'name' => 'Physics', 'icon' => '⚛️'],
            ['category_id' => $stem, 'name' => 'General Science', 'icon' => '🔬'],
            ['category_id' => $stem, 'name' => 'Agriculture', 'icon' => '🌾'],
            ['category_id' => $stem, 'name' => 'Computer Studies', 'icon' => '💻'],
            ['category_id' => $stem, 'name' => 'Home Science', 'icon' => '🏠'],
            ['category_id' => $stem, 'name' => 'Aviation', 'icon' => '✈️'],
            ['category_id' => $stem, 'name' => 'Building Construction', 'icon' => '🏗️'],
            ['category_id' => $stem, 'name' => 'Electricity', 'icon' => '⚡'],
            ['category_id' => $stem, 'name' => 'Metalwork', 'icon' => '🔧'],
            ['category_id' => $stem, 'name' => 'Power Mechanics', 'icon' => '⚙️'],
            ['category_id' => $stem, 'name' => 'Wood Technology', 'icon' => '🪵'],
            ['category_id' => $stem, 'name' => 'Media Technology', 'icon' => '📹'],
            ['category_id' => $stem, 'name' => 'Marine and Fisheries Technology', 'icon' => '🐟'],

            // ============ CBC SOCIAL SCIENCES PATHWAY (Grade 10-12) ============
            ['category_id' => $social, 'name' => 'Literature in English', 'icon' => '📚'],
            ['category_id' => $social, 'name' => 'Indigenous Languages', 'icon' => '🗣️'],
            ['category_id' => $social, 'name' => 'Fasihi ya Kiswahili', 'icon' => '📗'],
            ['category_id' => $social, 'name' => 'Sign Language', 'icon' => '🤟'],
            ['category_id' => $social, 'name' => 'Arabic', 'icon' => '🇸🇦'],
            ['category_id' => $social, 'name' => 'French', 'icon' => '🇫🇷'],
            ['category_id' => $social, 'name' => 'German', 'icon' => '🇩🇪'],
            ['category_id' => $social, 'name' => 'Mandarin Chinese', 'icon' => '🇨🇳'],
            ['category_id' => $social, 'name' => 'Christian Religious Education (CRE)', 'icon' => '✝️'],
            ['category_id' => $social, 'name' => 'Islamic Religious Education (IRE)', 'icon' => '☪️'],
            ['category_id' => $social, 'name' => 'Hindu Religious Education (HRE)', 'icon' => '🕉️'],
            ['category_id' => $social, 'name' => 'Business Studies', 'icon' => '💼'],
            ['category_id' => $social, 'name' => 'History and Citizenship', 'icon' => '🏛️'],
            ['category_id' => $social, 'name' => 'Geography', 'icon' => '🗺️'],

            // ============ CBC ARTS & SPORTS PATHWAY (Grade 10-12) ============
            ['category_id' => $arts, 'name' => 'Sports and Recreation', 'icon' => '⚽'],
            ['category_id' => $arts, 'name' => 'Music and Dance', 'icon' => '🎵'],
            ['category_id' => $arts, 'name' => 'Theatre and Film', 'icon' => '🎭'],
            ['category_id' => $arts, 'name' => 'Fine Arts', 'icon' => '🎨'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert([
                'category_id' => $subject['category_id'],
                'name' => $subject['name'],
                'slug' => Str::slug($subject['name']),
                'icon' => $subject['icon'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Successfully seeded ' . count($subjects) . ' subjects across ' . count($categories) . ' categories!');
        $this->command->info('📚 8-4-4 System: 29 subjects');
        $this->command->info('🎓 CBC Junior Secondary: 10 subjects');
        $this->command->info('⭐ CBC Senior Core: 6 subjects');
        $this->command->info('🔬 CBC STEM Pathway: 15 subjects');
        $this->command->info('🌍 CBC Social Sciences: 14 subjects');
        $this->command->info('🎨 CBC Arts & Sports: 4 subjects');
    }
}
