<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            // Per-paper structure: PP1, PP2, PP3, etc.
            if (!Schema::hasColumn('examiner_clinics', 'paper_type')) {
                $table->string('paper_type')->nullable()->after('subject_id');
            }
            // Link auto-created overflow sessions to their parent
            if (!Schema::hasColumn('examiner_clinics', 'parent_clinic_id')) {
                $table->foreignId('parent_clinic_id')
                    ->nullable()
                    ->after('paper_type')
                    ->constrained('examiner_clinics')
                    ->nullOnDelete();
            }
            // Track which batch this session is (1st, 2nd, 3rd filling of the same paper)
            if (!Schema::hasColumn('examiner_clinics', 'batch_number')) {
                $table->integer('batch_number')->default(1)->after('parent_clinic_id');
            }
        });

        // Update existing clinics to the new defaults
        DB::table('examiner_clinics')
            ->whereNull('max_students')
            ->orWhere('max_students', 50)
            ->update(['max_students' => 30]);

        DB::table('examiner_clinics')
            ->where('price', 1000)
            ->update(['price' => 300]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('examiner_clinics', 'parent_clinic_id')) {
            Schema::table('examiner_clinics', function (Blueprint $table) {
                $table->dropForeign(['parent_clinic_id']);
            });
        }

        $columns = array_values(array_filter(
            ['paper_type', 'parent_clinic_id', 'batch_number'],
            fn ($column) => Schema::hasColumn('examiner_clinics', $column)
        ));
        if ($columns) {
            Schema::table('examiner_clinics', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
