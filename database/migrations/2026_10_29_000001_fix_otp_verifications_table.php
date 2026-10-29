<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check what columns actually exist
        $columns = collect(DB::select('SHOW COLUMNS FROM otp_verifications'))
            ->pluck('Field')
            ->toArray();

        Schema::table('otp_verifications', function (Blueprint $table) use ($columns) {
            if (!in_array('identifier', $columns)) {
                $table->string('identifier')->after('id');
            }
            if (!in_array('type', $columns)) {
                $table->enum('type', ['email', 'phone'])->after('identifier');
            }
            if (!in_array('otp', $columns)) {
                $table->string('otp', 6)->after('type');
            }
            if (!in_array('verified', $columns)) {
                $table->boolean('verified')->default(false)->after('otp');
            }
            if (!in_array('attempts', $columns)) {
                $table->integer('attempts')->default(0)->after('verified');
            }
            if (!in_array('expires_at', $columns)) {
                $table->timestamp('expires_at')->after('attempts');
            }
        });

        // Add index if it doesn't exist
        try {
            DB::statement('ALTER TABLE otp_verifications ADD INDEX idx_identifier_type (identifier, type)');
        } catch (\Exception $e) {
            // Index already exists — ignore
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
