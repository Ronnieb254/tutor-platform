<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('examiner_clinics', function (Blueprint $table) {
        if (!Schema::hasColumn('examiner_clinics', 'bbb_meeting_id')) {
            $table->string('bbb_meeting_id')->nullable()->after('session_code');
        }
        if (!Schema::hasColumn('examiner_clinics', 'bbb_attendee_pw')) {
            $table->string('bbb_attendee_pw')->nullable()->after('bbb_meeting_id');
        }
        if (!Schema::hasColumn('examiner_clinics', 'bbb_moderator_pw')) {
            $table->string('bbb_moderator_pw')->nullable()->after('bbb_attendee_pw');
        }
    });

    Schema::table('bookings', function (Blueprint $table) {
        if (!Schema::hasColumn('bookings', 'bbb_meeting_id')) {
            $table->string('bbb_meeting_id')->nullable()->after('session_code');
        }
        if (!Schema::hasColumn('bookings', 'bbb_attendee_pw')) {
            $table->string('bbb_attendee_pw')->nullable()->after('bbb_meeting_id');
        }
        if (!Schema::hasColumn('bookings', 'bbb_moderator_pw')) {
            $table->string('bbb_moderator_pw')->nullable()->after('bbb_attendee_pw');
        }
    });
}

public function down(): void
{
    Schema::table('examiner_clinics', function (Blueprint $table) {
        $cols = array_filter(
            ['bbb_meeting_id', 'bbb_attendee_pw', 'bbb_moderator_pw'],
            fn ($c) => Schema::hasColumn('examiner_clinics', $c)
        );
        if ($cols) $table->dropColumn($cols);
    });

    Schema::table('bookings', function (Blueprint $table) {
        $cols = array_filter(
            ['bbb_meeting_id', 'bbb_attendee_pw', 'bbb_moderator_pw'],
            fn ($c) => Schema::hasColumn('bookings', $c)
        );
        if ($cols) $table->dropColumn($cols);
    });
}
};
