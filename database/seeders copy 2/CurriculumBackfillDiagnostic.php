<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurriculumBackfillDiagnostic extends Seeder
{
    public function run(): void
    {
        $cats = DB::table('categories')->select('id', 'slug', 'name')->get();
        $this->command->info("=== CATEGORIES ===");
        foreach ($cats as $c) {
            $this->command->line("  [{$c->id}] {$c->slug} — {$c->name}");
        }

        $null = DB::table('subjects')->whereNull('curriculum_id')->count();
        $set  = DB::table('subjects')->whereNotNull('curriculum_id')->count();
        $this->command->info("Subjects with NULL curriculum_id: {$null}, set: {$set}");

        $curricula = DB::table('curricula')->select('id', 'slug', 'track', 'name')->get();
        $this->command->info("=== CURRICULA ===");
        foreach ($curricula as $c) {
            $this->command->line("  [{$c->id}] {$c->slug} ({$c->track}) — {$c->name}");
        }
    }
}
