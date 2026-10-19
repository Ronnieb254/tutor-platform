<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    $cols = [
        'bbb_meeting_id'   => null,
        'bbb_attendee_pw'  => 'bbb_meeting_id',
        'bbb_moderator_pw' => 'bbb_attendee_pw',
    ];

    foreach (['examiner_clinics', 'bookings'] as $tableName) {
        Schema::table($tableName, function (Blueprint $table) use ($tableName, $cols) {
            foreach ($cols as $col => $prev) {
                if (Schema::hasColumn($tableName, $col)) {
                    continue;
                }
                $column = $table->string($col)->nullable();

                // Only position the column if the anchor exists
                $anchor = $prev ?? 'session_code';
                if (Schema::hasColumn($tableName, $anchor)) {
                    $column->after($anchor);
                }
            }
        });
    }
}

public function down(): void
{
    foreach (['examiner_clinics', 'bookings'] as $tableName) {
        $existing = array_filter(
            ['bbb_meeting_id', 'bbb_attendee_pw', 'bbb_moderator_pw'],
            fn ($c) => Schema::hasColumn($tableName, $c)
        );
        if ($existing) {
            Schema::table($tableName, fn (Blueprint $t) => $t->dropColumn(array_values($existing)));
        }
    }
}
};
