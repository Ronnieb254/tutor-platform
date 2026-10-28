<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            // Add auto-created flag
            $table->boolean('is_auto_created')->default(false)->after('status');
            
            // Ensure max_students defaults to 30
            $table->integer('max_students')->default(30)->change();
            
            // Add session code for Jitsi access
            if (!Schema::hasColumn('examiner_clinics', 'session_code')) {
                $table->string('session_code')->nullable()->after('meeting_room');
            }
            
            // Remove BBB-specific columns (no longer needed with Jitsi)
            if (Schema::hasColumn('examiner_clinics', 'bbb_meeting_id')) {
                $table->dropColumn(['bbb_meeting_id', 'bbb_attendee_pw', 'bbb_moderator_pw']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            $table->dropColumn('is_auto_created');
            
            if (!Schema::hasColumn('examiner_clinics', 'bbb_meeting_id')) {
                $table->string('bbb_meeting_id')->nullable();
                $table->string('bbb_attendee_pw')->nullable();
                $table->string('bbb_moderator_pw')->nullable();
            }
        });
    }
};
