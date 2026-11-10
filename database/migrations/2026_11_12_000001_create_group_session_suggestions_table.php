<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_session_suggestions', function (Blueprint $table) {
            $table->id();
            
            // 1. Raw column declarations
            $table->unsignedBigInteger('group_session_id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            $table->string('custom_subject', 255)->nullable();
            $table->string('area', 255); // Keep as string for indexing
            $table->integer('votes_count')->default(0);
            $table->timestamps();

            // 2. Set indices first to organize MySQL memory maps
            $table->unique(['group_session_id', 'area'], 'gs_suggestions_id_area_unique');
            $table->index(['group_session_id', 'votes_count']);

            // 3. Bind all Foreign Keys manually at the bottom
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('set null');

            $table->foreign('group_session_id')
                  ->references('id')
                  ->on('examiner_clinics')
                  ->onDelete('cascade');

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_session_suggestions');
    }
};
