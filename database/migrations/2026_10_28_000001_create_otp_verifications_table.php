<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');          // email or phone number
            $table->enum('type', ['email', 'phone']);
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
