<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            // BBB meeting credentials
            $table->string('bbb_meeting_id')->nullable()->after('session_code');
            $table->string('bbb_attendee_pw')->nullable()->after('bbb_meeting_id');
            $table->string('bbb_moderator_pw')->nullable()->after('bbb_attendee_pw');
        });

        // Also add to bookings for 1-on-1 sessions
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('bbb_meeting_id')->nullable()->after('session_code');
            $table->string('bbb_attendee_pw')->nullable()->after('bbb_meeting_id');
            $table->string('bbb_moderator_pw')->nullable()->after('bbb_attendee_pw');
        });
    }

    public function down(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            $table->dropColumn(['bbb_meeting_id', 'bbb_attendee_pw', 'bbb_moderator_pw']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['bbb_meeting_id', 'bbb_attendee_pw', 'bbb_moderator_pw']);
        });
    }
};
