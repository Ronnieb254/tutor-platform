<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('thumbnail')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('is_free')->default(false);
                $table->boolean('is_published')->default(false);
                $table->integer('total_lessons')->default(0);
                $table->integer('total_duration_seconds')->default(0);
                $table->string('level')->default('beginner');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('course_lessons')) {
            Schema::create('course_lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('video_url')->nullable();
                $table->integer('duration_seconds')->default(0);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_free_preview')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('course_enrollments')) {
            Schema::create('course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
                $table->integer('progress_percent')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->unique(['student_id', 'course_id']);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lesson_progress')) {
            Schema::create('lesson_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('lesson_id')->constrained('course_lessons')->cascadeOnDelete();
                $table->boolean('completed')->default(false);
                $table->integer('watch_seconds')->default(0);
                $table->unique(['student_id', 'lesson_id']);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('courses');
    }
};
