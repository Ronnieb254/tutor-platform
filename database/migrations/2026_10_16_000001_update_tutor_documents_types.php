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
        // Update existing document types if needed
        DB::table('tutor_documents')
            ->where('type', 'certificate')
            ->update(['type' => 'tsc_certificate']);
        
        DB::table('tutor_documents')
            ->where('type', 'qualification')
            ->update(['type' => 'academic_certificate']);
        
        // Note: Laravel will handle the enum validation in the model/controller
        // The 'type' column should allow these values: national_id, tsc_certificate, academic_certificate
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tutor_documents')
            ->where('type', 'tsc_certificate')
            ->update(['type' => 'certificate']);
        
        DB::table('tutor_documents')
            ->where('type', 'academic_certificate')
            ->update(['type' => 'qualification']);
    }
};
