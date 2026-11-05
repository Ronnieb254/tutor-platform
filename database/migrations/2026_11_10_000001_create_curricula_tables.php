<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // curricula table
        if (!Schema::hasTable('curricula')) {
            Schema::create('curricula', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('track', ['kenyan', 'international']);
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // curriculum_levels table
        if (!Schema::hasTable('curriculum_levels')) {
            Schema::create('curriculum_levels', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curriculum_id')->constrained('curricula')->cascadeOnDelete();
                $table->string('name');
                $table->string('code')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // tutor_curricula pivot
        if (!Schema::hasTable('tutor_curricula')) {
            Schema::create('tutor_curricula', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutor_profile_id')->constrained('tutor_profiles')->cascadeOnDelete();
                $table->foreignId('curriculum_id')->constrained('curricula')->cascadeOnDelete();
                $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                $table->unique(['tutor_profile_id', 'curriculum_id']);
            });
        }

        // learner_profiles table
        if (!Schema::hasTable('learner_profiles')) {
            Schema::create('learner_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('name');
                $table->foreignId('curriculum_id')->constrained('curricula')->cascadeOnDelete();
                $table->foreignId('level_id')->nullable()->constrained('curriculum_levels')->nullOnDelete();
                $table->enum('relationship', ['self', 'child', 'ward'])->default('self');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Add curriculum_id to subjects (nullable for backward compatibility)
        if (Schema::hasTable('subjects') && !Schema::hasColumn('subjects', 'curriculum_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->foreignId('curriculum_id')->nullable()->after('category_id')
                    ->constrained('curricula')->nullOnDelete();
                $table->index('curriculum_id');
            });
        }

        // Add curriculum_id and learner_profile_id to bookings
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'curriculum_id')) {
                    $table->foreignId('curriculum_id')->nullable()->after('subject_id')
                        ->constrained('curricula')->nullOnDelete();
                }
                if (!Schema::hasColumn('bookings', 'learner_profile_id')) {
                    $table->foreignId('learner_profile_id')->nullable()->after('curriculum_id')
                        ->constrained('learner_profiles')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'learner_profile_id')) {
                    $table->dropForeign(['learner_profile_id']);
                    $table->dropColumn('learner_profile_id');
                }
                if (Schema::hasColumn('bookings', 'curriculum_id')) {
                    $table->dropForeign(['curriculum_id']);
                    $table->dropColumn('curriculum_id');
                }
            });
        }
        if (Schema::hasTable('subjects') && Schema::hasColumn('subjects', 'curriculum_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropForeign(['curriculum_id']);
                $table->dropColumn('curriculum_id');
            });
        }
        Schema::dropIfExists('learner_profiles');
        Schema::dropIfExists('tutor_curricula');
        Schema::dropIfExists('curriculum_levels');
        Schema::dropIfExists('curricula');
    }
};
