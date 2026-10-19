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
            $table->string('paper_type')->nullable()->after('subject_id');
            // Link auto-created overflow sessions to their parent
            $table->foreignId('parent_clinic_id')
                ->nullable()
                ->after('paper_type')
                ->constrained('examiner_clinics')
                ->nullOnDelete();
            // Track which batch this session is (1st, 2nd, 3rd filling of the same paper)
            $table->integer('batch_number')->default(1)->after('parent_clinic_id');
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
        Schema::table('examiner_clinics', function (Blueprint $table) {
            $table->dropForeign(['parent_clinic_id']);
            $table->dropColumn(['paper_type', 'parent_clinic_id', 'batch_number']);
        });
    }
};
