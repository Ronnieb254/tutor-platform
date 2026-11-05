<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add curriculum_id to examiner_clinics (group sessions + clinics share this table)
        if (Schema::hasTable('examiner_clinics') && !Schema::hasColumn('examiner_clinics', 'curriculum_id')) {
            Schema::table('examiner_clinics', function (Blueprint $table) {
                $table->foreignId('curriculum_id')
                      ->nullable()
                      ->after('subject_id')
                      ->constrained('curricula')
                      ->nullOnDelete();
                $table->index('curriculum_id');
            });
        }

        // Add curriculum_id to blog_posts (library resources)
        if (Schema::hasTable('blog_posts') && !Schema::hasColumn('blog_posts', 'curriculum_id')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->foreignId('curriculum_id')
                      ->nullable()
                      ->after('subject_id')
                      ->constrained('curricula')
                      ->nullOnDelete();
                $table->index('curriculum_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blog_posts') && Schema::hasColumn('blog_posts', 'curriculum_id')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->dropForeign(['curriculum_id']);
                $table->dropIndex(['curriculum_id']);
                $table->dropColumn('curriculum_id');
            });
        }

        if (Schema::hasTable('examiner_clinics') && Schema::hasColumn('examiner_clinics', 'curriculum_id')) {
            Schema::table('examiner_clinics', function (Blueprint $table) {
                $table->dropForeign(['curriculum_id']);
                $table->dropIndex(['curriculum_id']);
                $table->dropColumn('curriculum_id');
            });
        }
    }
};
