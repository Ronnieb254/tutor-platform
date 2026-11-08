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
        Schema::table('payments', function (Blueprint $table) {
            // Payment gateway identifier
            $table->string('payment_gateway', 20)->default('imbank')->after('id');
            
            // I&M Bank transaction details
            $table->string('imbank_transaction_id')->nullable()->after('mpesa_reference');
            $table->string('imbank_reference')->nullable()->after('imbank_transaction_id');
            $table->string('imbank_receipt_number')->nullable()->after('imbank_reference');
            
            // Payment method used (mpesa, airtel_money, card, pesalink, etc.)
            $table->string('payment_method', 30)->nullable()->after('imbank_receipt_number');
            
            // Raw callback data for debugging
            $table->json('gateway_response')->nullable()->after('payment_method');
            
            // Add indexes for faster queries
            $table->index('payment_gateway');
            $table->index('imbank_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_gateway']);
            $table->dropIndex(['imbank_transaction_id']);
            
            $table->dropColumn([
                'payment_gateway',
                'imbank_transaction_id',
                'imbank_reference',
                'imbank_receipt_number',
                'payment_method',
                'gateway_response',
            ]);
        });
    }
};
