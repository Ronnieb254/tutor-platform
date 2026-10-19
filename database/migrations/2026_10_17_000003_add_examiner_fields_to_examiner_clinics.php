<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            // created_by = tutor who created this clinic (examiner)
            $table->foreignId('created_by')->nullable()->after('tutor_id')->constrained('users')->nullOnDelete();
            
            // session_code for Jitsi room identification
            $table->string('session_code', 100)->nullable()->after('meeting_room');
            
            // name field separate from title
            $table->string('name')->nullable()->after('subject_id');
            
            // recording_url to store Jitsi recording
            $table->string('recording_url')->nullable()->after('session_code');
        });

        // Backfill created_by from tutor_id for existing records
        DB::table('examiner_clinics')->whereNull('created_by')->update(['created_by' => DB::raw('tutor_id')]);
    }

    public function down(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by', 'session_code', 'name', 'recording_url']);
        });
    }
};
