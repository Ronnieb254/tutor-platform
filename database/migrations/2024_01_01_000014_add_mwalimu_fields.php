<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add phone OTP and referral to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_verified_at')->nullable()->after('phone');
            $table->string('referral_code', 20)->nullable()->unique()->after('provider_id');
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete()->after('referral_code');
        });

        // Add TSC and additional doc fields to tutor_profiles
        Schema::table('tutor_profiles', function (Blueprint $table) {
            $table->boolean('is_tsc_registered')->default(false)->after('is_approved');
            $table->string('tsc_number')->nullable()->after('is_tsc_registered');
            $table->boolean('speedy_verification_paid')->default(false)->after('tsc_number');
            $table->timestamp('speedy_verification_paid_at')->nullable()->after('speedy_verification_paid');
            $table->json('subjects_taught')->nullable()->after('speedy_verification_paid_at'); // max 2 subjects
            $table->enum('level', ['JSS','SS','F3','F4','all'])->default('all')->after('subjects_taught');
            $table->integer('referral_earnings')->default(0)->after('level');
        });

        // Add more doc types to tutor_documents
        Schema::table('tutor_documents', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('tutor_documents', function (Blueprint $table) {
            $table->enum('type', [
                'national_id','degree_certificate','tsc_certificate',
                'certificate_of_good_conduct','passport_photo','other'
            ])->after('tutor_profile_id');
        });

        // Examiner's Clinic table
        Schema::create('examiner_clinics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('topics_covered')->nullable(); // exam strategy, marking scheme, etc
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->decimal('price', 10, 2)->default(1000.00);
            $table->integer('max_students')->default(50);
            $table->enum('status', ['scheduled','live','completed','cancelled'])->default('scheduled');
            $table->string('meeting_room')->nullable();
            $table->timestamps();
            $table->index(['subject_id', 'scheduled_at']);
        });

        // Clinic enrollments
        Schema::create('clinic_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('examiner_clinics')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_code', 20)->nullable()->unique();
            $table->boolean('attended')->default(false);
            $table->unique(['clinic_id', 'student_id']);
            $table->timestamps();
        });

        // Add fixed pricing fields to bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('need_type')->nullable()->after('student_notes'); // explanation, homework, exam prep, etc
            $table->string('topic')->nullable()->after('need_type');
            $table->enum('level', ['JSS','SS','F3','F4'])->nullable()->after('topic');
        });

        // Referral commissions
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_tutor_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('status', ['pending','paid'])->default('pending');
            $table->timestamps();
        });

        // OTP verification table
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('otp', 6);
            $table->string('purpose')->default('login'); // login, register, payment
            $table->boolean('verified')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
        Schema::dropIfExists('referral_commissions');
        Schema::dropIfExists('clinic_enrollments');
        Schema::dropIfExists('examiner_clinics');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['need_type','topic','level']);
        });
        Schema::table('tutor_profiles', function (Blueprint $table) {
            $table->dropColumn(['is_tsc_registered','tsc_number','speedy_verification_paid','speedy_verification_paid_at','subjects_taught','level','referral_earnings']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_verified_at','referral_code','referred_by']);
        });
    }
};
