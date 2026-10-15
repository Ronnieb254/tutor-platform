<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tutor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->integer('experience_years')->default(0);
            $table->json('languages')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_sessions')->default(0);
            $table->string('video_intro')->nullable();
            $table->json('certificates')->nullable();
            $table->enum('subscription_plan', ['free', 'pro', 'elite'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tutor_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unique(['tutor_profile_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_subjects');
        Schema::dropIfExists('tutor_profiles');
    }
};
