<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tutor_contract_acceptances')) {
            Schema::create('tutor_contract_acceptances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('contract_version')->default('v1.0');
                $table->string('contract_hash'); // SHA-256 hash of the contract content
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('accepted_at');
                $table->timestamps();
            
                $table->index(['user_id', 'contract_version']);
            });
        }

        // Add contract acceptance flag to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('contract_accepted')->default(false)->after('remember_token');
            $table->timestamp('contract_accepted_at')->nullable()->after('contract_accepted');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['contract_accepted', 'contract_accepted_at']);
        });
        
        Schema::dropIfExists('tutor_contract_acceptances');
    }
};
