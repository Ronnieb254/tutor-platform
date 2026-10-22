<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TutorProfile;
use App\Models\User;
use App\Models\Wallet;
use App\Models\PlatformSetting;
use App\Notifications\BookingConfirmedStudent;
use App\Notifications\BookingConfirmedTutor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    public function __construct(protected BigBlueButtonService $bbb) {}

    public function calculateFees(float $amount): array
    {
        $commissionRate = (float) PlatformSetting::get('commission_rate', 0.15);
        $platformFee    = round($amount * $commissionRate, 2);
        $tutorEarning   = round($amount - $platformFee, 2);

        return [
            'total_amount'  => $amount,
            'platform_fee'  => $platformFee,
            'tutor_earning' => $tutorEarning,
        ];
    }

    public function createBooking(array $data): Booking
    {
        // Fixed pricing — check free first session for 15-min
        $studentId = $data['student_id'];
        $tutorProfile = TutorProfile::findOrFail($data['tutor_profile_id']);
        $minutes  = $data['duration_minutes'];

        $isFree   = ($minutes === 15) && TutorProfile::isFreeFirstSession($studentId, $tutorProfile->user_id);
        $amount   = $isFree ? 0.00 : ($tutorProfile->getPriceForDuration($minutes));
        $fees     = $this->calculateFees($amount);

        return DB::transaction(function () use ($data, $fees, $tutorProfile) {
            $uuid = (string) Str::uuid();

            $booking = Booking::create([
                'uuid'            => $uuid,
                'student_id'      => $data['student_id'],
                'tutor_id'        => $tutorProfile->user_id,
                'subject_id'      => $data['subject_id'] ?? null,
                'scheduled_at'    => $data['scheduled_at'],
                'duration_minutes'=> $data['duration_minutes'],
                'status'          => 'pending',
                'meeting_room'    => 'Mwalimu-session-' . $uuid,
                'total_amount'    => $fees['total_amount'],
                'platform_fee'    => $fees['platform_fee'],
                'tutor_earning'   => $fees['tutor_earning'],
                'student_notes'   => $data['notes'] ?? null,
                'booking_for'     => $data['booking_for'] ?? 'self',
                'child_profile_id'=> $data['child_profile_id'] ?? null,
            ]);

            return $booking;
        });
    }

    public function confirmBooking(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            // Generate session access codes
            $tutorCode   = 'TUT-' . strtoupper(Str::random(6));
            $studentCode = 'STU-' . strtoupper(Str::random(6));

            // Generate meeting room name and passwords
            $meetingID = 'mwalimu-' . $booking->uuid;
            $attendeePW = $this->bbb->generatePassword(8);
            $moderatorPW = $this->bbb->generatePassword(8);

            // Create BigBlueButton meeting
            $meetingName = "Session with " . $booking->tutor->name;
            $this->bbb->createMeeting(
                $meetingID,
                $meetingName,
                $attendeePW,
                $moderatorPW,
                [
                    'welcome' => 'Welcome to your Mwalimu tutoring session!',
                    'logoutURL' => route('dashboard'),
                    'record' => 'true',
                    'autoStartRecording' => 'false',
                ]
            );

            // Generate join URLs (students use attendee password, tutors use moderator password)
            $tutorJoinUrl = $this->bbb->getJoinUrl(
                $meetingID,
                $booking->tutor->name,
                $moderatorPW,
                true,
                'tutor-' . $booking->tutor_id
            );

            $studentJoinUrl = $this->bbb->getJoinUrl(
                $meetingID,
                $booking->student->name,
                $attendeePW,
                false,
                'student-' . $booking->student_id
            );

            $booking->update([
                'status'               => 'confirmed',
                'meeting_room'         => $meetingID,
                'meeting_url'          => $studentJoinUrl, // Store student URL (or we can dynamically generate)
                'meeting_password'     => $attendeePW, // Store for regenerating join URLs
                'moderator_password'   => $moderatorPW, // For tutor
                'session_code_tutor'   => $tutorCode,
                'session_code_student' => $studentCode,
            ]);

            if ((float) $booking->tutor_earning > 0) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $booking->tutor_id],
                    ['balance' => 0, 'currency' => 'KES']
                );
                $wallet->credit(
                    $booking->tutor_earning,
                    "Earnings from session #{$booking->uuid}",
                    $booking->uuid
                );
            }

            // Update tutor stats
            $booking->tutor->tutorProfile?->increment('total_sessions');

            $booking->refresh()->load(['student', 'tutor', 'subject']);

            try {
                $booking->student->notify(new BookingConfirmedStudent($booking));
            } catch (\Throwable $e) {
                Log::warning('Student booking notification failed: '.$e->getMessage());
            }

            try {
                $booking->tutor->notify(new BookingConfirmedTutor($booking));
            } catch (\Throwable $e) {
                Log::warning('Tutor booking notification failed: '.$e->getMessage());
            }
        });
    }

    public function cancelBooking(Booking $booking, string $reason = '', ?User $cancelledBy = null): void
    {
        $booking->update([
            'status'               => 'cancelled',
            'cancellation_reason'  => $reason,
            'cancelled_at'         => now(),
        ]);
    }

    public function completeBooking(Booking $booking): void
    {
        $booking->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);
    }
}
