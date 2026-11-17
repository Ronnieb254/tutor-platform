<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('curriculum_preference', 20)
                  ->nullable()
                  ->default(null)
                  ->after('timezone')
                  ->comment('User\'s chosen curriculum: kenyan | international');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('curriculum_preference');
        });
    }
};
