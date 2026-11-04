<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Schema::getIndexes('subjects') as $index) {
            if (($index['unique'] ?? false) && $index['columns'] === ['slug']) {
                Schema::table('subjects', function (Blueprint $table) use ($index) {
                    $table->dropUnique($index['name']);
                });
            }
        }

        if (
            !Schema::hasIndex('subjects', 'subjects_category_slug_unique')
            && !Schema::hasIndex('subjects', ['category_id', 'slug'], 'unique')
        ) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->unique(['category_id', 'slug'], 'subjects_category_slug_unique');
            });
        }
    }

    public function down(): void
    {
        foreach (Schema::getIndexes('subjects') as $index) {
            if (($index['unique'] ?? false) && $index['columns'] === ['category_id', 'slug']) {
                Schema::table('subjects', function (Blueprint $table) use ($index) {
                    $table->dropUnique($index['name']);
                });
            }
        }

        if (
            !Schema::hasIndex('subjects', 'subjects_slug_unique')
            && !Schema::hasIndex('subjects', ['slug'], 'unique')
        ) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->unique('slug', 'subjects_slug_unique');
            });
        }
    }
};
