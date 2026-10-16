<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'booking_for')) {
                $table->enum('booking_for', ['self', 'child'])->default('self')->after('student_notes');
            }
            if (! Schema::hasColumn('bookings', 'child_profile_id')) {
                $table->foreignId('child_profile_id')->nullable()->after('booking_for')->constrained('child_profiles')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'session_code_tutor')) {
                $table->string('session_code_tutor', 20)->nullable()->after('tutor_joined');
            }
            if (! Schema::hasColumn('bookings', 'session_code_student')) {
                $table->string('session_code_student', 20)->nullable()->after('session_code_tutor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'child_profile_id')) {
                $table->dropConstrainedForeignId('child_profile_id');
            }
            $drop = [];
            foreach (['booking_for', 'session_code_tutor', 'session_code_student'] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $drop[] = $column;
                }
            }
            if ($drop) {
                $table->dropColumn($drop);
            }
        });
    }
};
