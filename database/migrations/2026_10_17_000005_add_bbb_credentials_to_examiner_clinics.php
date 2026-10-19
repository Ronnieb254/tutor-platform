<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = ['bbb_meeting_id', 'bbb_attendee_pw', 'bbb_moderator_pw'];

    public function up(): void
    {
        $this->addColumns('examiner_clinics', 'session_code');
        $this->addColumns('bookings', 'meeting_room');
    }

    public function down(): void
    {
        foreach (['examiner_clinics', 'bookings'] as $tableName) {
            $existing = array_values(array_filter(
                $this->columns,
                fn ($c) => Schema::hasColumn($tableName, $c)
            ));
            if ($existing) {
                Schema::table($tableName, fn (Blueprint $t) => $t->dropColumn($existing));
            }
        }
    }

    private function addColumns(string $tableName, string $anchor): void
    {
        $prev = $anchor;

        foreach ($this->columns as $col) {
            if (!Schema::hasColumn($tableName, $col)) {
                Schema::table($tableName, function (Blueprint $table) use ($col, $prev) {
                    $table->string($col)->nullable()->after($prev);
                });
            }
            $prev = $col;
        }
    }
}
;