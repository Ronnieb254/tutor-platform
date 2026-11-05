<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Attendance timestamps (replaces boolean flags – booleans kept for BC)
            if (!Schema::hasColumn('bookings', 'tutor_joined_at')) {
                $table->timestamp('tutor_joined_at')->nullable()->after('tutor_joined');
            }
            if (!Schema::hasColumn('bookings', 'student_joined_at')) {
                $table->timestamp('student_joined_at')->nullable()->after('tutor_joined_at');
            }
            // Seconds both parties were connected
            if (!Schema::hasColumn('bookings', 'actual_duration_seconds')) {
                $table->unsignedInteger('actual_duration_seconds')->nullable()->after('duration_minutes');
            }
            // Delivery classification
            if (!Schema::hasColumn('bookings', 'delivery_status')) {
                $table->enum('delivery_status', [
                    'pending',
                    'fully_delivered',
                    'partially_delivered',
                    'not_delivered',
                ])->nullable()->after('completed_at');
            }
            // Tutor earnings settled flag
            if (!Schema::hasColumn('bookings', 'earnings_settled')) {
                $table->boolean('earnings_settled')->default(false)->after('delivery_status');
            }
            // Student refund credited flag
            if (!Schema::hasColumn('bookings', 'refund_credited')) {
                $table->boolean('refund_credited')->default(false)->after('earnings_settled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            foreach ([
                'tutor_joined_at', 'student_joined_at',
                'actual_duration_seconds', 'delivery_status',
                'earnings_settled', 'refund_credited',
            ] as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
