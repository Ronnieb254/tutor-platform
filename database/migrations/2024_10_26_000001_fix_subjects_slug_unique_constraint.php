<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Schema::getIndexes('subjects') as $index) {
            if ($index['type'] === 'unique' && $index['columns'] === ['slug']) {
                Schema::table('subjects', function (Blueprint $table) use ($index) {
                    $table->dropUnique($index['name']);
                });
            }
        }

        $hasCategorySlugUnique = false;
        foreach (Schema::getIndexes('subjects') as $index) {
            if ($index['type'] === 'unique' && $index['columns'] === ['category_id', 'slug']) {
                $hasCategorySlugUnique = true;
                break;
            }
        }

        if (! $hasCategorySlugUnique) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->unique(['category_id', 'slug'], 'subjects_category_slug_unique');
            });
        }
    }

    public function down(): void
    {
        foreach (Schema::getIndexes('subjects') as $index) {
            if ($index['type'] === 'unique' && $index['columns'] === ['category_id', 'slug']) {
                Schema::table('subjects', function (Blueprint $table) use ($index) {
                    $table->dropUnique($index['name']);
                });
            }
        }

        $hasSlugUnique = false;
        foreach (Schema::getIndexes('subjects') as $index) {
            if ($index['type'] === 'unique' && $index['columns'] === ['slug']) {
                $hasSlugUnique = true;
                break;
            }
        }

        if (! $hasSlugUnique) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->unique('slug', 'subjects_slug_unique');
            });
        }
    }
};
