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
        Schema::table('examiner_clinics', function (Blueprint $table) {
            if (!Schema::hasColumn('examiner_clinics', 'series_id')) {
                $table->string('series_id')->nullable()->after('is_group_session');
            }
            if (!Schema::hasColumn('examiner_clinics', 'session_number')) {
                $table->integer('session_number')->nullable()->after('series_id');
            }
            if (!Schema::hasColumn('examiner_clinics', 'total_sessions')) {
                $table->integer('total_sessions')->nullable()->after('session_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examiner_clinics', function (Blueprint $table) {
            if (Schema::hasColumn('examiner_clinics', 'series_id')) {
                $table->dropColumn('series_id');
            }
            if (Schema::hasColumn('examiner_clinics', 'session_number')) {
                $table->dropColumn('session_number');
            }
            if (Schema::hasColumn('examiner_clinics', 'total_sessions')) {
                $table->dropColumn('total_sessions');
            }
        });
    }
};
