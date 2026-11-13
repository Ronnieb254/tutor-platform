<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix the status column to properly support both clinic and group session statuses.
     * MySQL was incorrectly treating the column as an enum.
     */
    public function up(): void
    {
        // First, check if column exists and what type it is
        $columns = DB::select("SHOW FULL COLUMNS FROM examiner_clinics WHERE Field = 'status'");
        
        if (empty($columns)) {
            return; // Column doesn't exist, skip
        }

        // Check if it's an ENUM that's too restrictive
        $column = $columns[0];
        
        if (strpos($column->Type, 'enum') !== false) {
            // It's an ENUM, we need to modify it to TEXT
            DB::statement('ALTER TABLE examiner_clinics MODIFY status VARCHAR(50) DEFAULT "scheduled"');
        } elseif ($column->Type === 'varchar(255)') {
            // Already varchar, but might have constraint issues - ensure it's proper
            DB::statement('ALTER TABLE examiner_clinics MODIFY status VARCHAR(100) DEFAULT "scheduled"');
        }
    }

    public function down(): void
    {
        // Revert to string if needed
        if (Schema::hasTable('examiner_clinics')) {
            DB::statement('ALTER TABLE examiner_clinics MODIFY status VARCHAR(255) DEFAULT "scheduled"');
        }
    }
};
