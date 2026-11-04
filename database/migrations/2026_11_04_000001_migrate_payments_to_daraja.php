<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces Paystack columns on the payments table with Daraja (M-Pesa) columns.
 *
 * Old columns renamed / kept for reference:
 *   paystack_reference   → mpesa_reference
 *   paystack_access_code → dropped (not needed for STK Push)
 *
 * New columns added:
 *   checkout_request_id  — Daraja's CheckoutRequestID (for callback matching)
 *   merchant_request_id  — Daraja's MerchantRequestID
 *   mpesa_receipt        — M-Pesa receipt number (from successful callback)
 *   payer_phone          — Customer's phone in 254XXXXXXXXX format
 *   failure_reason       — Short description of why payment failed
 *   course_id            — FK for course payments
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new Daraja columns
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'mpesa_reference')) {
                $table->string('mpesa_reference')->nullable()->unique()->after('commission_rate');
            }

            if (!Schema::hasColumn('payments', 'checkout_request_id')) {
                $table->string('checkout_request_id')->nullable()->index()->after('mpesa_reference');
            }

            if (!Schema::hasColumn('payments', 'merchant_request_id')) {
                $table->string('merchant_request_id')->nullable()->after('checkout_request_id');
            }

            if (!Schema::hasColumn('payments', 'mpesa_receipt')) {
                $table->string('mpesa_receipt')->nullable()->after('merchant_request_id');
            }

            if (!Schema::hasColumn('payments', 'payer_phone')) {
                $table->string('payer_phone', 20)->nullable()->after('mpesa_receipt');
            }

            if (!Schema::hasColumn('payments', 'failure_reason')) {
                $table->string('failure_reason')->nullable()->after('payer_phone');
            }

            if (!Schema::hasColumn('payments', 'course_id')) {
                $table->unsignedBigInteger('course_id')->nullable()->after('clinic_id');
            }
        });

        // Step 2: Migrate data from paystack_reference → mpesa_reference
        // (This must happen AFTER the columns exist, in a separate transaction)
        if (Schema::hasColumn('payments', 'paystack_reference')) {
            \DB::statement(
                'UPDATE payments SET mpesa_reference = paystack_reference WHERE mpesa_reference IS NULL AND paystack_reference IS NOT NULL'
            );
        }

        // Step 3: Drop old Paystack columns
        Schema::table('payments', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('payments', 'paystack_reference')) {
                $dropCols[] = 'paystack_reference';
            }
            if (Schema::hasColumn('payments', 'paystack_access_code')) {
                $dropCols[] = 'paystack_access_code';
            }
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Restore Paystack columns
            if (!Schema::hasColumn('payments', 'paystack_reference')) {
                $table->string('paystack_reference')->nullable()->unique();
            }
            if (!Schema::hasColumn('payments', 'paystack_access_code')) {
                $table->string('paystack_access_code')->nullable();
            }

            // Drop Daraja columns
            $drop = ['checkout_request_id', 'merchant_request_id', 'mpesa_receipt',
                     'payer_phone', 'failure_reason'];
            foreach ($drop as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
