<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KenyanCurriculumSeederSafe extends Seeder
{
    /**
     * Safe version - only inserts if not exists, doesn't delete anything
     */
    public function run(): void
    {
        $categories = [
            ['name' => '8-4-4 System (Form 1-4)', 'slug' => '8-4-4-system', 'icon' => '📚', 'description' => 'Kenya Certificate of Secondary Education (KCSE) subjects for Form 1-4', 'sort_order' => 1],
            ['name' => 'CBC Junior Secondary (Grade 7-9)', 'slug' => 'cbc-junior-secondary', 'icon' => '🎓', 'description' => 'Competency-Based Curriculum for Junior Secondary School (Grade 7, 8, 9)', 'sort_order' => 2],
            ['name' => 'CBC Senior School Core (Grade 10-12)', 'slug' => 'cbc-senior-core', 'icon' => '⭐', 'description' => 'Core subjects for all Senior School students', 'sort_order' => 3],
            ['name' => 'CBC STEM Pathway (Grade 10-12)', 'slug' => 'cbc-stem-pathway', 'icon' => '🔬', 'description' => 'Science, Technology, Engineering and Mathematics pathway subjects', 'sort_order' => 4],
            ['name' => 'CBC Social Sciences (Grade 10-12)', 'slug' => 'cbc-social-sciences', 'icon' => '🌍', 'description' => 'Social Sciences pathway subjects including languages and humanities', 'sort_order' => 5],
            ['name' => 'CBC Arts & Sports (Grade 10-12)', 'slug' => 'cbc-arts-sports', 'icon' => '🎨', 'description' => 'Arts and Sports Science pathway subjects', 'sort_order' => 6],
        ];

        $insertedCategories = 0;
        foreach ($categories as $category) {
            $exists = DB::table('categories')->where('slug', $category['slug'])->exists();
            if (!$exists) {
                DB::table('categories')->insert(array_merge($category, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $insertedCategories++;
            }
        }

        // Get category IDs by slug
        $categoryMap = DB::table('categories')
            ->whereIn('slug', ['8-4-4-system', 'cbc-junior-secondary', 'cbc-senior-core', 'cbc-stem-pathway', 'cbc-social-sciences', 'cbc-arts-sports'])
            ->pluck('id', 'slug')
            ->toArray();

        $subjects = [
            // 8-4-4 System
            [$categoryMap['8-4-4-system'], 'English', '📖'],
            [$categoryMap['8-4-4-system'], 'Kiswahili', '📗'],
            [$categoryMap['8-4-4-system'], 'Mathematics Alternative A', '🔢'],
            [$categoryMap['8-4-4-system'], 'Mathematics Alternative B', '📐'],
            [$categoryMap['8-4-4-system'], 'Biology', '🧬'],
            [$categoryMap['8-4-4-system'], 'Physics', '⚛️'],
            [$categoryMap['8-4-4-system'], 'Chemistry', '🧪'],
            [$categoryMap['8-4-4-system'], 'History and Government', '🏛️'],
            [$categoryMap['8-4-4-system'], 'Geography', '🗺️'],
            [$categoryMap['8-4-4-system'], 'Christian Religious Education (CRE)', '✝️'],
            [$categoryMap['8-4-4-system'], 'Islamic Religious Education (IRE)', '☪️'],
            [$categoryMap['8-4-4-system'], 'Hindu Religious Education (HRE)', '🕉️'],
            [$categoryMap['8-4-4-system'], 'Home Science', '🏠'],
            [$categoryMap['8-4-4-system'], 'Art and Design', '🎨'],
            [$categoryMap['8-4-4-system'], 'Agriculture', '🌾'],
            [$categoryMap['8-4-4-system'], 'Woodwork', '🪵'],
            [$categoryMap['8-4-4-system'], 'Metalwork', '🔧'],
            [$categoryMap['8-4-4-system'], 'Building Construction', '🏗️'],
            [$categoryMap['8-4-4-system'], 'Power Mechanics', '⚙️'],
            [$categoryMap['8-4-4-system'], 'Electricity', '⚡'],
            [$categoryMap['8-4-4-system'], 'Drawing and Design', '📏'],
            [$categoryMap['8-4-4-system'], 'Aviation Technology', '✈️'],
            [$categoryMap['8-4-4-system'], 'Computer Studies', '💻'],
            [$categoryMap['8-4-4-system'], 'French', '🇫🇷'],
            [$categoryMap['8-4-4-system'], 'German', '🇩🇪'],
            [$categoryMap['8-4-4-system'], 'Arabic', '🇸🇦'],
            [$categoryMap['8-4-4-system'], 'Kenyan Sign Language', '🤟'],
            [$categoryMap['8-4-4-system'], 'Music', '🎵'],
            [$categoryMap['8-4-4-system'], 'Business Studies', '💼'],

            // CBC Junior Secondary
            [$categoryMap['cbc-junior-secondary'], 'English', '📖'],
            [$categoryMap['cbc-junior-secondary'], 'Kiswahili', '📗'],
            [$categoryMap['cbc-junior-secondary'], 'Kenyan Sign Language (KSL)', '🤟'],
            [$categoryMap['cbc-junior-secondary'], 'Mathematics', '🔢'],
            [$categoryMap['cbc-junior-secondary'], 'Integrated Science', '🔬'],
            [$categoryMap['cbc-junior-secondary'], 'Social Studies', '🌍'],
            [$categoryMap['cbc-junior-secondary'], 'Religious Education', '📿'],
            [$categoryMap['cbc-junior-secondary'], 'Agriculture', '🌾'],
            [$categoryMap['cbc-junior-secondary'], 'Pre-Technical Studies', '🔧'],
            [$categoryMap['cbc-junior-secondary'], 'Creative Arts and Sports', '🎨'],

            // CBC Senior Core
            [$categoryMap['cbc-senior-core'], 'English', '📖'],
            [$categoryMap['cbc-senior-core'], 'Kiswahili', '📗'],
            [$categoryMap['cbc-senior-core'], 'Kenyan Sign Language (KSL)', '🤟'],
            [$categoryMap['cbc-senior-core'], 'Core Mathematics', '🔢'],
            [$categoryMap['cbc-senior-core'], 'Essential Mathematics', '📐'],
            [$categoryMap['cbc-senior-core'], 'Community Service Learning (CSL)', '🤝'],

            // CBC STEM
            [$categoryMap['cbc-stem-pathway'], 'Biology', '🧬'],
            [$categoryMap['cbc-stem-pathway'], 'Chemistry', '🧪'],
            [$categoryMap['cbc-stem-pathway'], 'Physics', '⚛️'],
            [$categoryMap['cbc-stem-pathway'], 'General Science', '🔬'],
            [$categoryMap['cbc-stem-pathway'], 'Agriculture', '🌾'],
            [$categoryMap['cbc-stem-pathway'], 'Computer Studies', '💻'],
            [$categoryMap['cbc-stem-pathway'], 'Home Science', '🏠'],
            [$categoryMap['cbc-stem-pathway'], 'Aviation', '✈️'],
            [$categoryMap['cbc-stem-pathway'], 'Building Construction', '🏗️'],
            [$categoryMap['cbc-stem-pathway'], 'Electricity', '⚡'],
            [$categoryMap['cbc-stem-pathway'], 'Metalwork', '🔧'],
            [$categoryMap['cbc-stem-pathway'], 'Power Mechanics', '⚙️'],
            [$categoryMap['cbc-stem-pathway'], 'Wood Technology', '🪵'],
            [$categoryMap['cbc-stem-pathway'], 'Media Technology', '📹'],
            [$categoryMap['cbc-stem-pathway'], 'Marine and Fisheries Technology', '🐟'],

            // CBC Social Sciences
            [$categoryMap['cbc-social-sciences'], 'Literature in English', '📚'],
            [$categoryMap['cbc-social-sciences'], 'Indigenous Languages', '🗣️'],
            [$categoryMap['cbc-social-sciences'], 'Fasihi ya Kiswahili', '📗'],
            [$categoryMap['cbc-social-sciences'], 'Sign Language', '🤟'],
            [$categoryMap['cbc-social-sciences'], 'Arabic', '🇸🇦'],
            [$categoryMap['cbc-social-sciences'], 'French', '🇫🇷'],
            [$categoryMap['cbc-social-sciences'], 'German', '🇩🇪'],
            [$categoryMap['cbc-social-sciences'], 'Mandarin Chinese', '🇨🇳'],
            [$categoryMap['cbc-social-sciences'], 'Christian Religious Education (CRE)', '✝️'],
            [$categoryMap['cbc-social-sciences'], 'Islamic Religious Education (IRE)', '☪️'],
            [$categoryMap['cbc-social-sciences'], 'Hindu Religious Education (HRE)', '🕉️'],
            [$categoryMap['cbc-social-sciences'], 'Business Studies', '💼'],
            [$categoryMap['cbc-social-sciences'], 'History and Citizenship', '🏛️'],
            [$categoryMap['cbc-social-sciences'], 'Geography', '🗺️'],

            // CBC Arts & Sports
            [$categoryMap['cbc-arts-sports'], 'Sports and Recreation', '⚽'],
            [$categoryMap['cbc-arts-sports'], 'Music and Dance', '🎵'],
            [$categoryMap['cbc-arts-sports'], 'Theatre and Film', '🎭'],
            [$categoryMap['cbc-arts-sports'], 'Fine Arts', '🎨'],
        ];

        $insertedSubjects = 0;
        foreach ($subjects as [$categoryId, $name, $icon]) {
            $baseSlug = Str::slug($name);
            
            // Check if this exact subject already exists (same category + name)
            $exists = DB::table('subjects')
                ->where('category_id', $categoryId)
                ->where('name', $name)
                ->exists();
            
            if ($exists) {
                continue; // Skip if already exists
            }
            
            // Make slug unique across all subjects by checking and appending category
            $slug = $baseSlug;
            $slugExists = DB::table('subjects')->where('slug', $slug)->exists();
            
            if ($slugExists) {
                // Append category slug to make it unique
                $categorySlug = DB::table('categories')->where('id', $categoryId)->value('slug');
                $slug = $baseSlug . '-' . substr($categorySlug, 0, 10); // e.g., english-8-4-4-sys
                
                // If still exists, add a number
                $counter = 1;
                while (DB::table('subjects')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . substr($categorySlug, 0, 10) . '-' . $counter;
                    $counter++;
                }
            }
            
            try {
                DB::table('subjects')->insert([
                    'category_id' => $categoryId,
                    'name' => $name,
                    'slug' => $slug,
                    'icon' => $icon,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertedSubjects++;
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Skipped duplicate: $name (category $categoryId)");
            }
        }

        $this->command->info("✅ Safe seeding complete!");
        $this->command->info("📊 Categories: $insertedCategories new (skipped existing)");
        $this->command->info("📚 Subjects: $insertedSubjects new (skipped existing)");
    }
}
