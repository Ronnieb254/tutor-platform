<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tutor_documents', function (Blueprint $table) {
            $table->enum('type', [
                'national_id',
                'certificate',
                'qualification',
                'degree_certificate',
                'tsc_certificate',
                'certificate_of_good_conduct',
                'passport_photo',
                'other',
            ])->change();
        });

        // Update existing document types if needed
        DB::table('tutor_documents')
            ->where('type', 'certificate')
            ->update(['type' => 'tsc_certificate']);
        
        DB::table('tutor_documents')
            ->where('type', 'qualification')
            ->update(['type' => 'degree_certificate']);

        Schema::table('tutor_documents', function (Blueprint $table) {
            $table->enum('type', [
                'national_id',
                'degree_certificate',
                'tsc_certificate',
                'certificate_of_good_conduct',
                'passport_photo',
                'other',
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutor_documents', function (Blueprint $table) {
            $table->enum('type', [
                'national_id',
                'certificate',
                'qualification',
                'degree_certificate',
                'tsc_certificate',
                'certificate_of_good_conduct',
                'passport_photo',
                'other',
            ])->change();
        });

        DB::table('tutor_documents')
            ->where('type', 'tsc_certificate')
            ->update(['type' => 'certificate']);
        
        DB::table('tutor_documents')
            ->where('type', 'degree_certificate')
            ->update(['type' => 'qualification']);

        Schema::table('tutor_documents', function (Blueprint $table) {
            $table->enum('type', [
                'national_id',
                'certificate',
                'qualification',
                'other',
            ])->change();
        });
    }
};
