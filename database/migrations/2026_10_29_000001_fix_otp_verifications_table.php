<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop and recreate with correct schema.
        // OTPs are ephemeral (10-min expiry) — no real data loss.
        Schema::dropIfExists('otp_verifications');

        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');               // email address or phone number
            $table->enum('type', ['email', 'phone']);   // which channel
            $table->string('otp', 6);
            $table->boolean('verified')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['identifier', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
