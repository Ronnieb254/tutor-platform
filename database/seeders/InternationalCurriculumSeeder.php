<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds international-curriculum subjects (IGCSE Cambridge, IGCSE Edexcel, IB MYP, IB Diploma)
 * with curriculum_id set so track-filtering works on the public browse pages.
 *
 * Safe to run repeatedly — it only inserts subjects that don't already exist for that curriculum.
 */
class InternationalCurriculumSeeder extends Seeder
{
    public function run(): void
    {
        // Resolve curriculum IDs from the curricula table
        $curricula = DB::table('curricula')->pluck('id', 'slug');

        $igcseCambridge = $curricula['igcse-cambridge'] ?? null;
        $igcseEdexcel   = $curricula['igcse-edexcel']   ?? null;
        $ibMyp          = $curricula['ib-myp']           ?? null;
        $ibDiploma      = $curricula['ib-diploma']       ?? null;

        if (!$igcseCambridge || !$igcseEdexcel || !$ibMyp || !$ibDiploma) {
            $this->command->error('International curricula not found in DB. Run CurriculumSeeder first.');
            return;
        }

        // ── Create or reuse a single "International" category ──────────────────
        $intlCatId = DB::table('categories')->where('slug', 'international')->value('id');
        if (!$intlCatId) {
            $intlCatId = DB::table('categories')->insertGetId([
                'name'        => 'International',
                'slug'        => 'international',
                'icon'        => '🌐',
                'description' => 'Subjects for international curricula (IGCSE, IB)',
                'is_active'   => true,
                'sort_order'  => 10,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ── Subject lists ───────────────────────────────────────────────────────
        $igcseSubjects = [
            ['name' => 'Mathematics',           'icon' => '🔢'],
            ['name' => 'Additional Mathematics','icon' => '📐'],
            ['name' => 'English Language',      'icon' => '📖'],
            ['name' => 'English Literature',    'icon' => '📚'],
            ['name' => 'Biology',               'icon' => '🧬'],
            ['name' => 'Chemistry',             'icon' => '🧪'],
            ['name' => 'Physics',               'icon' => '⚛️'],
            ['name' => 'Combined Science',      'icon' => '🔬'],
            ['name' => 'History',               'icon' => '🏛️'],
            ['name' => 'Geography',             'icon' => '🗺️'],
            ['name' => 'Economics',             'icon' => '💹'],
            ['name' => 'Business Studies',      'icon' => '💼'],
            ['name' => 'Computer Science',      'icon' => '💻'],
            ['name' => 'ICT',                   'icon' => '🖥️'],
            ['name' => 'Accounting',            'icon' => '📊'],
            ['name' => 'French',                'icon' => '🇫🇷'],
            ['name' => 'Spanish',               'icon' => '🇪🇸'],
            ['name' => 'Art & Design',          'icon' => '🎨'],
        ];

        $ibMypSubjects = [
            ['name' => 'Language & Literature',          'icon' => '📖'],
            ['name' => 'Language Acquisition',           'icon' => '🗣️'],
            ['name' => 'Individuals & Societies',        'icon' => '🌍'],
            ['name' => 'Sciences',                       'icon' => '🔬'],
            ['name' => 'Mathematics',                    'icon' => '🔢'],
            ['name' => 'Arts',                           'icon' => '🎨'],
            ['name' => 'Physical & Health Education',    'icon' => '🏃'],
            ['name' => 'Design',                         'icon' => '📐'],
        ];

        $ibDiplomaSubjects = [
            ['name' => 'English A Literature',           'icon' => '📚'],
            ['name' => 'English A Language & Literature','icon' => '📖'],
            ['name' => 'History',                        'icon' => '🏛️'],
            ['name' => 'Geography',                      'icon' => '🗺️'],
            ['name' => 'Economics',                      'icon' => '💹'],
            ['name' => 'Biology HL',                     'icon' => '🧬'],
            ['name' => 'Biology SL',                     'icon' => '🧬'],
            ['name' => 'Chemistry HL',                   'icon' => '🧪'],
            ['name' => 'Chemistry SL',                   'icon' => '🧪'],
            ['name' => 'Physics HL',                     'icon' => '⚛️'],
            ['name' => 'Physics SL',                     'icon' => '⚛️'],
            ['name' => 'Mathematics AA HL',              'icon' => '🔢'],
            ['name' => 'Mathematics AA SL',              'icon' => '🔢'],
            ['name' => 'Mathematics AI HL',              'icon' => '📐'],
            ['name' => 'Mathematics AI SL',              'icon' => '📐'],
            ['name' => 'Computer Science',               'icon' => '💻'],
            ['name' => 'Business Management',            'icon' => '💼'],
            ['name' => 'Psychology',                     'icon' => '🧠'],
            ['name' => 'Visual Arts',                    'icon' => '🎨'],
        ];

        $inserted = 0;

        // Insert IGCSE Cambridge subjects
        foreach ($igcseSubjects as $s) {
            $exists = DB::table('subjects')
                ->where('curriculum_id', $igcseCambridge)
                ->where('name', $s['name'])
                ->exists();
            if (!$exists) {
                DB::table('subjects')->insert([
                    'category_id'   => $intlCatId,
                    'curriculum_id' => $igcseCambridge,
                    'name'          => $s['name'],
                    'slug'          => Str::slug($s['name']) . '-igcse-cambridge',
                    'icon'          => $s['icon'],
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                $inserted++;
            }
        }

        // Insert IGCSE Edexcel subjects
        foreach ($igcseSubjects as $s) {
            $exists = DB::table('subjects')
                ->where('curriculum_id', $igcseEdexcel)
                ->where('name', $s['name'])
                ->exists();
            if (!$exists) {
                DB::table('subjects')->insert([
                    'category_id'   => $intlCatId,
                    'curriculum_id' => $igcseEdexcel,
                    'name'          => $s['name'],
                    'slug'          => Str::slug($s['name']) . '-igcse-edexcel',
                    'icon'          => $s['icon'],
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                $inserted++;
            }
        }

        // Insert IB MYP subjects
        foreach ($ibMypSubjects as $s) {
            $exists = DB::table('subjects')
                ->where('curriculum_id', $ibMyp)
                ->where('name', $s['name'])
                ->exists();
            if (!$exists) {
                DB::table('subjects')->insert([
                    'category_id'   => $intlCatId,
                    'curriculum_id' => $ibMyp,
                    'name'          => $s['name'],
                    'slug'          => Str::slug($s['name']) . '-ib-myp',
                    'icon'          => $s['icon'],
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                $inserted++;
            }
        }

        // Insert IB Diploma subjects
        foreach ($ibDiplomaSubjects as $s) {
            $exists = DB::table('subjects')
                ->where('curriculum_id', $ibDiploma)
                ->where('name', $s['name'])
                ->exists();
            if (!$exists) {
                DB::table('subjects')->insert([
                    'category_id'   => $intlCatId,
                    'curriculum_id' => $ibDiploma,
                    'name'          => $s['name'],
                    'slug'          => Str::slug($s['name']) . '-ib-diploma',
                    'icon'          => $s['icon'],
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                $inserted++;
            }
        }

        $this->command->info("✅ Seeded {$inserted} international subjects across 4 curricula.");
        $this->command->info('   IGCSE Cambridge: ' . count($igcseSubjects) . ' subjects');
        $this->command->info('   IGCSE Edexcel:   ' . count($igcseSubjects) . ' subjects');
        $this->command->info('   IB MYP:          ' . count($ibMypSubjects) . ' subjects');
        $this->command->info('   IB Diploma:      ' . count($ibDiplomaSubjects) . ' subjects');
    }
}
