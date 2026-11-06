<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            $table->string('tutor_code', 20)->nullable()->unique()->after('session_code');
        });
    }

    public function down(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            $table->dropColumn('tutor_code');
        });
    }
};
