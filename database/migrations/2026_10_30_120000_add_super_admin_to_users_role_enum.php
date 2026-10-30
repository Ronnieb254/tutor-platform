<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'super-admin' to the users.role enum.
     */
    public function up(): void
    {
        // MySQL: modify enum in-place
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student','tutor','admin','parent','super-admin') NOT NULL DEFAULT 'student'");
    }

    public function down(): void
    {
        // First move any super-admin users back to admin so the column can shrink
        DB::table('users')->where('role', 'super-admin')->update(['role' => 'admin']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student','tutor','admin','parent') NOT NULL DEFAULT 'student'");
    }
};
