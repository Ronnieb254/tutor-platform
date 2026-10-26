<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the unique constraint on slug alone
            $table->dropUnique('subjects_slug_unique');
            
            // Add a composite unique constraint: slug must be unique within each category
            $table->unique(['category_id', 'slug'], 'subjects_category_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Reverse the change
            $table->dropUnique('subjects_category_slug_unique');
            $table->unique('slug', 'subjects_slug_unique');
        });
    }
};
