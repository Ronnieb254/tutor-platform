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
        if (!Schema::hasColumn('examiner_clinics', 'is_group_session')) {
            Schema::table('examiner_clinics', function (Blueprint $table) {
                $table->boolean('is_group_session')->default(false)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('examiner_clinics', 'is_group_session')) {
            Schema::table('examiner_clinics', function (Blueprint $table) {
                $table->dropColumn('is_group_session');
            });
        }
    }
};
