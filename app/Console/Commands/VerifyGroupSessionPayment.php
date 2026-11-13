<?php

namespace App\Console\Commands;

use App\Models\ClinicEnrollment;
use App\Models\Payment;
use App\Notifications\GroupSessionEnrollmentConfirmed;
use App\Notifications\StudentEnrolledInGroupSession;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class VerifyGroupSessionPayment extends Command
{
    protected $signature = 'group-sessions:verify-payment {payment_id} {--code=}';
    protected $description = 'Verify a group session payment and send notifications to student and tutor';

    public function handle()
    {
        $paymentId = $this->argument('payment_id');
        $paymentCode = $this->option('code');

        $payment = Payment::findOrFail($paymentId);

        if ($payment->status === 'success') {
            $this->error('Payment already verified.');
            return Command::FAILURE;
        }

        if ($payment->payment_type !== 'group_session') {
            $this->error('This is not a group session payment.');
            return Command::FAILURE;
        }

        // Verify the payment code if provided
        if ($paymentCode && strtoupper($paymentCode) !== $payment->mpesa_code) {
            $this->error('Payment code does not match.');
            return Command::FAILURE;
        }

        // Mark payment as verified
        $payment->update([
            'status' => 'success',
            'verified_at' => now(),
        ]);

        // Get the enrollment
        $enrollment = ClinicEnrollment::where('payment_id', $payment->id)->first();

        if (!$enrollment) {
            $this->error('Enrollment not found.');
            return Command::FAILURE;
        }

        // Generate session code for the student
        $sessionCode = 'SES-' . strtoupper(Str::random(8));

        // Update enrollment status and add session code
        $enrollment->update([
            'status' => 'confirmed',
            'session_code' => $sessionCode,
        ]);

        // Send notification to student
        $student = $enrollment->student;
        $student->notify(new GroupSessionEnrollmentConfirmed($enrollment, $sessionCode));

        $this->info('✓ Student notified at: ' . $student->email);

        // Send notification to tutor
        $tutor = $enrollment->clinic->tutor;
        if ($tutor) {
            $tutor->notify(new StudentEnrolledInGroupSession($enrollment));
            $this->info('✓ Tutor notified at: ' . $tutor->email);
        }

        $this->info('✓ Payment verified successfully');
        $this->line('✓ Session code: ' . $sessionCode);

        return Command::SUCCESS;
    }
}
