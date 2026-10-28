<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add library columns to blog_posts
        Schema::table('blog_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_posts', 'resource_type')) {
                $table->enum('resource_type', ['article', 'pdf', 'video', 'notes', 'past_paper'])
                      ->default('article')
                      ->after('meta_description');
            }
            if (!Schema::hasColumn('blog_posts', 'file_path')) {
                $table->string('file_path')->nullable()->after('resource_type');
            }
            if (!Schema::hasColumn('blog_posts', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('blog_posts', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->after('file_size')
                      ->constrained('subjects')->nullOnDelete();
            }
            if (!Schema::hasColumn('blog_posts', 'grade_level')) {
                $table->string('grade_level', 20)->nullable()->after('subject_id');
            }
            if (!Schema::hasColumn('blog_posts', 'download_count')) {
                $table->unsignedInteger('download_count')->default(0)->after('grade_level');
            }
            if (!Schema::hasColumn('blog_posts', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('download_count');
            }
        });

        // Add library columns to blog_categories
        Schema::table('blog_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_categories', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('blog_categories', 'icon')) {
                $table->string('icon', 50)->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $columns = ['resource_type', 'file_path', 'file_size', 'subject_id', 'grade_level', 'download_count', 'is_approved'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('blog_posts', $column)) {
                    if ($column === 'subject_id') {
                        $table->dropForeign(['subject_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('blog_categories', function (Blueprint $table) {
            foreach (['description', 'icon'] as $column) {
                if (Schema::hasColumn('blog_categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
