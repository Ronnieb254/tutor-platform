<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('email', 255);
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->enum('status', ['success', 'failed']);
            $table->string('reason')->nullable();
            $table->timestamp('created_at');
            
            $table->index('user_id');
            $table->index('email');
            $table->index(['email', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_activities');
    }
};
