<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Backfills curriculum_id on all existing Kenyan subjects by mapping
 * category slug → curriculum slug.
 *
 * Safe to run multiple times — only updates rows WHERE curriculum_id IS NULL.
 * Uses the actual category slugs found in the production DB.
 */
class SubjectCurriculumBackfillSeeder extends Seeder
{
    public function run(): void
    {
        $curricula  = DB::table('curricula')->pluck('id', 'slug');
        $categories = DB::table('categories')->pluck('id', 'slug');

        // Map category slug → curriculum slug.
        // Covers both the old KenyanCurriculumSeeder slugs (8-4-4-system, cbc-*)
        // and the real DB slugs discovered via the diagnostic seeder.
        $map = [
            // Old KenyanCurriculumSeeder slugs (in case they exist)
            '8-4-4-system'           => '8-4-4',
            'cbc-junior-secondary'   => 'cbe-junior-secondary',
            'cbc-senior-core'        => 'cbe-senior-school',
            'cbc-stem-pathway'       => 'cbe-senior-school',
            'cbc-social-sciences'    => 'cbe-senior-school',
            'cbc-arts-sports'        => 'cbe-senior-school',

            // Real DB slugs discovered in production
            'form-3-4-kcse-8-4-4'          => '8-4-4',
            'jss-grade-7-8-9'              => 'cbe-junior-secondary',
            'senior-school-core-g10-12'    => 'cbe-senior-school',
            'senior-school-stem-pathway'   => 'cbe-senior-school',
            'senior-school-social-sciences'=> 'cbe-senior-school',
            'senior-school-arts-sports'    => 'cbe-senior-school',

            // Generic subject categories — map broadly to 8-4-4 for backward compat
            'mathematics'  => '8-4-4',
            'sciences'     => '8-4-4',
            'languages'    => '8-4-4',
            'humanities'   => '8-4-4',
            'business'     => '8-4-4',
        ];

        $updated = 0;
        foreach ($map as $catSlug => $currSlug) {
            $catId  = $categories[$catSlug] ?? null;
            $currId = $curricula[$currSlug]  ?? null;
            if ($catId && $currId) {
                $rows = DB::table('subjects')
                    ->where('category_id', $catId)
                    ->whereNull('curriculum_id')
                    ->update(['curriculum_id' => $currId]);
                $updated += $rows;
            }
        }

        $this->command->info("✅ Backfilled curriculum_id on {$updated} Kenyan subjects.");
    }
}
