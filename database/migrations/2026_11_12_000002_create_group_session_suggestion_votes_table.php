<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_session_suggestion_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suggestion_id')->constrained('group_session_suggestions')->onDelete('cascade');
            $table->foreignId('voter_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('voter_ip', 45)->nullable();
            $table->string('browser_id', 36)->nullable();
            $table->timestamps();

            // UNIQUE constraints for deduplication per voter type
            // A voter can only vote once per suggestion via their voter_id
            $table->unique(['suggestion_id', 'voter_id']);
            // A guest can only vote once per suggestion via their IP
            $table->unique(['suggestion_id', 'voter_ip']);
            // A guest can only vote once per suggestion via their browser_id
            $table->unique(['suggestion_id', 'browser_id']);

            // Index for quick lookups by suggestion
            $table->index('suggestion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_session_suggestion_votes');
    }
};
