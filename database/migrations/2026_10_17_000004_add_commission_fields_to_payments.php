<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Type of payment: 'booking' (1-on-1 session) or 'clinic' (exam clinic)
            $table->enum('payment_type', ['booking', 'clinic'])->default('booking')->after('amount');
            
            // Commission calculation
            $table->decimal('tutor_amount', 10, 2)->nullable()->after('payment_type'); // 75% for bookings, 40% for clinics
            $table->decimal('platform_amount', 10, 2)->nullable()->after('tutor_amount'); // 25% for bookings, 60% for clinics
            $table->decimal('commission_rate', 5, 2)->default(25.00)->after('platform_amount'); // Percentage to platform
            
            // Foreign key to clinic payment (if clinic type)
            $table->foreignId('clinic_id')->nullable()->after('booking_id')->constrained('examiner_clinics')->nullOnDelete();
        });

        // Backfill existing payments as 'booking' type with 25% commission
        DB::table('payments')
            ->whereNull('payment_type')
            ->update([
                'payment_type' => 'booking',
                'commission_rate' => 25.00,
                'tutor_amount' => DB::raw('amount * 0.75'),
                'platform_amount' => DB::raw('amount * 0.25'),
            ]);
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn(['payment_type', 'tutor_amount', 'platform_amount', 'commission_rate', 'clinic_id']);
        });
    }
};
