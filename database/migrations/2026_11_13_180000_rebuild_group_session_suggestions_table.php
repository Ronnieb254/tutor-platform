<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key constraints temporarily
        Schema::disableForeignKeyConstraints();
        
        try {
            // Drop the old table if it exists with the wrong schema
            if (Schema::hasTable('group_session_suggestions')) {
                // Check if it has the old columns (group_session_id, area, etc.)
                if (Schema::hasColumn('group_session_suggestions', 'group_session_id')) {
                    Schema::dropIfExists('group_session_suggestions');
                }
            }
            
            // Create the correct table
            if (!Schema::hasTable('group_session_suggestions')) {
                Schema::create('group_session_suggestions', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    $table->string('email')->nullable();
                    $table->string('topic');
                    $table->text('description')->nullable();
                    $table->ipAddress('ip_address')->nullable();
                    $table->timestamps();
                    
                    $table->index('created_at');
                    $table->index('user_id');
                });
            }
        } finally {
            // Re-enable foreign key constraints
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        try {
            Schema::dropIfExists('group_session_suggestions');
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
};
