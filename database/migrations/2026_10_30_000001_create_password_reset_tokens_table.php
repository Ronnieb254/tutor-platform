<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This migration ensures the password_reset_tokens table exists with the
 * correct structure. The table was originally created in the users migration
 * (2024_01_01_000001), so we skip creation if it already exists.
 */
return new class extends Migration {
    public function up(): void
    {
        // Table already exists from the original users migration — skip safely
        if (Schema::hasTable('password_reset_tokens')) {
            return;
        }

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        // Do not drop — it was created by the original users migration
    }
};
