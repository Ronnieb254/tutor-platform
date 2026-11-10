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
            $table->foreignId('group_session_id')->constrained('examiner_clinics')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->string('custom_subject', 255)->nullable();
            $table->text('area');
            $table->integer('votes_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // UNIQUE index on (group_session_id, area) to prevent duplicate suggestions
            $table->unique(['group_session_id', 'area']);

            // Composite index for efficient sorting by votes
            $table->index(['group_session_id', 'votes_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_session_suggestions');
    }
};
