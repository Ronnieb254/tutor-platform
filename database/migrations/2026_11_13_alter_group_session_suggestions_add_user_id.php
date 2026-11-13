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
        // If the table doesn't exist at all, create it from scratch
        if (!Schema::hasTable('group_session_suggestions')) {
            Schema::create('group_session_suggestions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('email')->nullable();
                $table->string('topic');
                $table->text('description')->nullable();
                $table->ipAddress('ip_address')->nullable();
                $table->timestamps();
                
                $table->index('created_at');
                $table->index('user_id');
            });
        } else {
            // Table exists, add missing columns
            Schema::table('group_session_suggestions', function (Blueprint $table) {
                if (!Schema::hasColumn('group_session_suggestions', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
                }
                
                if (!Schema::hasColumn('group_session_suggestions', 'email')) {
                    $table->string('email')->nullable()->after('user_id');
                }
                
                if (!Schema::hasColumn('group_session_suggestions', 'topic')) {
                    $table->string('topic')->after('email');
                }
                
                if (!Schema::hasColumn('group_session_suggestions', 'description')) {
                    $table->text('description')->nullable()->after('topic');
                }
                
                if (!Schema::hasColumn('group_session_suggestions', 'ip_address')) {
                    $table->ipAddress('ip_address')->nullable()->after('description');
                }
                
                if (!Schema::hasColumn('group_session_suggestions', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_session_suggestions');
    }
};
