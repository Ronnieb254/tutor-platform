<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'meeting_password')) {
                $table->string('meeting_password')->nullable()->after('meeting_url');
            }
            if (!Schema::hasColumn('bookings', 'moderator_password')) {
                $table->string('moderator_password')->nullable()->after('meeting_password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = array_values(array_filter(
            ['meeting_password', 'moderator_password'],
            fn ($column) => Schema::hasColumn('bookings', $column)
        ));

        if ($columns) {
            Schema::table('bookings', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
