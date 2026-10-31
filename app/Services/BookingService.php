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
    public function __construct(protected JitsiService $jitsi) {}

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
        // Fixed pricing
        $studentId = $data['student_id'];
        $tutorProfile = TutorProfile::findOrFail($data['tutor_profile_id']);
        $minutes  = $data['duration_minutes'];
        $sessionType = $data['session_type'] ?? 'individual';

        $amount   = $tutorProfile->getPriceForSession($minutes, $sessionType);
        $fees     = $this->calculateFees($amount);

        return DB::transaction(function () use ($data, $fees, $tutorProfile, $sessionType) {
            $uuid = (string) Str::uuid();

            $booking = Booking::create([
                'uuid'            => $uuid,
                'student_id'      => $data['student_id'],
                'tutor_id'        => $tutorProfile->user_id,
                'subject_id'      => $data['subject_id'] ?? null,
                'scheduled_at'    => $data['scheduled_at'],
                'duration_minutes'=> $data['duration_minutes'],
                'session_type'    => $sessionType,
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
        // Generate codes and meeting details outside transaction first
        $tutorCode   = 'TUT-' . strtoupper(Str::random(6));
        $studentCode = 'STU-' . strtoupper(Str::random(6));
        $meetingID   = 'Mwalimu-session-' . $booking->uuid;

        $tutorJoinUrl = $this->jitsi->getJoinUrl(
            $meetingID,
            $booking->tutor->name,
            true,
            ['email' => $booking->tutor->email, 'recording' => true]
        );

        $studentJoinUrl = $this->jitsi->getJoinUrl(
            $meetingID,
            $booking->student->name,
            false,
            ['email' => $booking->student->email, 'restrictGuests' => true]
        );

        // Keep the DB transaction tight — only data writes, no external calls
        DB::transaction(function () use ($booking, $tutorCode, $studentCode, $meetingID, $tutorJoinUrl, $studentJoinUrl) {
            $booking->update([
                'status'               => 'confirmed',
                'meeting_room'         => $meetingID,
                'meeting_url'          => $studentJoinUrl,
                'meeting_password'     => null,
                'moderator_password'   => null,
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

            $booking->tutor->tutorProfile?->increment('total_sessions');
        });

        // Reload fresh data for notifications — outside the transaction
        $booking->refresh()->load(['student', 'tutor', 'subject']);

        try {
            $booking->student->notify(new BookingConfirmedStudent($booking));
        } catch (\Throwable $e) {
            Log::warning('Student booking notification failed: ' . $e->getMessage());
        }

        try {
            $booking->tutor->notify(new BookingConfirmedTutor($booking));
        } catch (\Throwable $e) {
            Log::warning('Tutor booking notification failed: ' . $e->getMessage());
        }
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
