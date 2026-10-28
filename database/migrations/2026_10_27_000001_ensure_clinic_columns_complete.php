<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ensure all columns required by the 30-slot clinic booking system are present.
 * This migration is idempotent – every column is guarded by hasColumn().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {

            // Core identity / display
            if (!Schema::hasColumn('examiner_clinics', 'title')) {
                $table->string('title')->nullable()->after('name');
            }

            // Duration – drives the "30-minute break" overflow time calculation
            if (!Schema::hasColumn('examiner_clinics', 'duration_minutes')) {
                $table->unsignedSmallInteger('duration_minutes')->default(60)->after('scheduled_at');
            }

            // Overflow / batch tracking
            if (!Schema::hasColumn('examiner_clinics', 'parent_clinic_id')) {
                $table->foreignId('parent_clinic_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('examiner_clinics')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('examiner_clinics', 'batch_number')) {
                $table->unsignedSmallInteger('batch_number')->default(1)->after('parent_clinic_id');
            }

            if (!Schema::hasColumn('examiner_clinics', 'is_auto_created')) {
                $table->boolean('is_auto_created')->default(false)->after('status');
            }

            // Jitsi room identifier
            if (!Schema::hasColumn('examiner_clinics', 'meeting_room')) {
                $table->string('meeting_room')->nullable()->after('status');
            }

            // Session code used as Jitsi room name
            if (!Schema::hasColumn('examiner_clinics', 'session_code')) {
                $table->string('session_code')->nullable()->after('meeting_room');
            }

            // Capacity – default 30
            if (!Schema::hasColumn('examiner_clinics', 'max_students')) {
                $table->unsignedSmallInteger('max_students')->default(30)->after('price');
            }

            // Who created the clinic (admin or examiner)
            if (!Schema::hasColumn('examiner_clinics', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->after('tutor_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Topics (JSON array)
            if (!Schema::hasColumn('examiner_clinics', 'topics_covered')) {
                $table->json('topics_covered')->nullable()->after('description');
            }

            // Recording URL saved after session
            if (!Schema::hasColumn('examiner_clinics', 'recording_url')) {
                $table->string('recording_url')->nullable()->after('session_code');
            }

            // Status column (scheduled / cancelled / completed)
            if (!Schema::hasColumn('examiner_clinics', 'status')) {
                $table->string('status')->default('scheduled')->after('max_students');
            }
        });
    }

    public function down(): void
    {
        // Intentionally left without drops – columns may be used by existing data.
        // To roll back, write a targeted migration.
    }
};
