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
            if (!Schema::hasColumn('payments', 'payment_type')) {
                $table->enum('payment_type', ['booking', 'clinic'])->default('booking')->after('amount');
            }
            
            // Commission calculation
            if (!Schema::hasColumn('payments', 'tutor_amount')) {
                $table->decimal('tutor_amount', 10, 2)->nullable()->after('payment_type');
            }
            if (!Schema::hasColumn('payments', 'platform_amount')) {
                $table->decimal('platform_amount', 10, 2)->nullable()->after('tutor_amount');
            }
            if (!Schema::hasColumn('payments', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(25.00)->after('platform_amount');
            }
            
            // Foreign key to clinic payment (if clinic type)
            if (!Schema::hasColumn('payments', 'clinic_id')) {
                $table->foreignId('clinic_id')->nullable()->after('booking_id')->constrained('examiner_clinics')->nullOnDelete();
            }
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
        if (Schema::hasColumn('payments', 'clinic_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['clinic_id']);
            });
        }

        $columns = array_values(array_filter(
            ['payment_type', 'tutor_amount', 'platform_amount', 'commission_rate', 'clinic_id'],
            fn ($column) => Schema::hasColumn('payments', $column)
        ));
        if ($columns) {
            Schema::table('payments', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
