<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Curriculum;
use App\Models\CurriculumLevel;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if already seeded
        if (Curriculum::count() > 0) {
            $this->command->info('Curricula already seeded, skipping.');
            return;
        }

        $curricula = [
            // Kenyan track
            [
                'name'        => '8-4-4',
                'track'       => 'kenyan',
                'slug'        => '8-4-4',
                'description' => 'Kenya Certificate of Secondary Education (KCSE) – Form 1 to Form 4.',
                'sort_order'  => 1,
                'is_active'   => true,
                'levels' => [
                    ['name' => 'Form 1', 'code' => 'F1', 'sort_order' => 1],
                    ['name' => 'Form 2', 'code' => 'F2', 'sort_order' => 2],
                    ['name' => 'Form 3', 'code' => 'F3', 'sort_order' => 3],
                    ['name' => 'Form 4', 'code' => 'F4', 'sort_order' => 4],
                ],
            ],
            [
                'name'        => 'CBE Junior Secondary',
                'track'       => 'kenyan',
                'slug'        => 'cbe-junior-secondary',
                'description' => 'Competency-Based Education – Junior Secondary School (Grade 7–9).',
                'sort_order'  => 2,
                'is_active'   => true,
                'levels' => [
                    ['name' => 'Grade 7', 'code' => 'G7', 'sort_order' => 1],
                    ['name' => 'Grade 8', 'code' => 'G8', 'sort_order' => 2],
                    ['name' => 'Grade 9', 'code' => 'G9', 'sort_order' => 3],
                ],
            ],
            [
                'name'        => 'CBE Senior School',
                'track'       => 'kenyan',
                'slug'        => 'cbe-senior-school',
                'description' => 'Competency-Based Education – Senior School (Grade 10–12).',
                'sort_order'  => 3,
                'is_active'   => true,
                'levels' => [
                    ['name' => 'Grade 10', 'code' => 'G10', 'sort_order' => 1],
                    ['name' => 'Grade 11', 'code' => 'G11', 'sort_order' => 2],
                    ['name' => 'Grade 12', 'code' => 'G12', 'sort_order' => 3],
                ],
            ],

            // International track
            [
                'name'        => 'IGCSE Cambridge',
                'track'       => 'international',
                'slug'        => 'igcse-cambridge',
                'description' => 'Cambridge Assessment International Education – IGCSE.',
                'sort_order'  => 4,
                'is_active'   => true,
                'levels' => [
                    ['name' => 'Year 10', 'code' => 'Y10', 'sort_order' => 1],
                    ['name' => 'Year 11', 'code' => 'Y11', 'sort_order' => 2],
                ],
            ],
            [
                'name'        => 'IGCSE Edexcel',
                'track'       => 'international',
                'slug'        => 'igcse-edexcel',
                'description' => 'Pearson Edexcel International GCSE.',
                'sort_order'  => 5,
                'is_active'   => true,
                'levels' => [
                    ['name' => 'Year 10', 'code' => 'Y10', 'sort_order' => 1],
                    ['name' => 'Year 11', 'code' => 'Y11', 'sort_order' => 2],
                ],
            ],
            [
                'name'        => 'IB MYP',
                'track'       => 'international',
                'slug'        => 'ib-myp',
                'description' => 'International Baccalaureate – Middle Years Programme (MYP).',
                'sort_order'  => 6,
                'is_active'   => true,
                'levels' => [
                    ['name' => 'MYP Year 1', 'code' => 'MYP1', 'sort_order' => 1],
                    ['name' => 'MYP Year 2', 'code' => 'MYP2', 'sort_order' => 2],
                    ['name' => 'MYP Year 3', 'code' => 'MYP3', 'sort_order' => 3],
                    ['name' => 'MYP Year 4', 'code' => 'MYP4', 'sort_order' => 4],
                    ['name' => 'MYP Year 5', 'code' => 'MYP5', 'sort_order' => 5],
                ],
            ],
            [
                'name'        => 'IB Diploma',
                'track'       => 'international',
                'slug'        => 'ib-diploma',
                'description' => 'International Baccalaureate – Diploma Programme (DP), with SL/HL subjects.',
                'sort_order'  => 7,
                'is_active'   => true,
                'levels' => [
                    ['name' => 'DP Year 1', 'code' => 'DP1', 'sort_order' => 1],
                    ['name' => 'DP Year 2', 'code' => 'DP2', 'sort_order' => 2],
                ],
            ],
        ];

        foreach ($curricula as $data) {
            $levels = $data['levels'];
            unset($data['levels']);

            $curriculum = Curriculum::create($data);

            foreach ($levels as $level) {
                $curriculum->levels()->create($level);
            }
        }

        $this->command->info('✅ Seeded 7 curricula with levels: 8-4-4, CBE Junior Secondary, CBE Senior School, IGCSE Cambridge, IGCSE Edexcel, IB MYP, IB Diploma.');
    }
}
