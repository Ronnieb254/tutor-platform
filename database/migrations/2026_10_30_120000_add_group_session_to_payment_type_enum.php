<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'payment_type')) {
            return;
        }

        // MySQL: alter enum to add 'group_session'
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('booking','clinic','group_session','speedy_verification','course') DEFAULT 'booking'");
    }

    public function down(): void
    {
        if (!Schema::hasColumn('payments', 'payment_type')) {
            return;
        }

        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('booking','clinic','speedy_verification','course') DEFAULT 'booking'");
    }
};
