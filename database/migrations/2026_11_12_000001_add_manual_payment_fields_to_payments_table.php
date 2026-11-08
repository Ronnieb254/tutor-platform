<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('mpesa_code')->nullable()->after('mpesa_receipt');
            $table->text('admin_note')->nullable()->after('mpesa_code');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('admin_note');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });

        // Extend the status enum to include pending_verification
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','pending_verification','success','failed','refunded') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum to original set
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','success','failed','refunded') NOT NULL DEFAULT 'pending'");

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['mpesa_code', 'admin_note', 'verified_by', 'verified_at']);
        });
    }
};
