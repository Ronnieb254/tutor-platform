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
        // Fixed pricing — check free first session for 20-min individual
        $studentId = $data['student_id'];
        $tutorProfile = TutorProfile::findOrFail($data['tutor_profile_id']);
        $minutes  = $data['duration_minutes'];
        $sessionType = $data['session_type'] ?? 'individual';

        // Free first session only applies to 20-min individual sessions
        $isFree   = ($sessionType === 'individual' && $minutes === 20) 
                    && TutorProfile::isFreeFirstSession($studentId, $tutorProfile->user_id);
        $amount   = $isFree ? 0.00 : ($tutorProfile->getPriceForSession($minutes, $sessionType));
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
        DB::transaction(function () use ($booking) {
            // Generate session access codes
            $tutorCode   = 'TUT-' . strtoupper(Str::random(6));
            $studentCode = 'STU-' . strtoupper(Str::random(6));

            // Generate Jitsi meeting room name (no passwords needed for Jitsi)
            $meetingID = 'Mwalimu-session-' . $booking->uuid;

            // Note: Jitsi meetings don't need to be "created" - they exist when someone joins
            // The room is secured by the unique UUID in the room name
            
            // Generate join URLs
            $tutorJoinUrl = $this->jitsi->getJoinUrl(
                $meetingID,
                $booking->tutor->name,
                true, // isModerator
                [
                    'email' => $booking->tutor->email,
                    'recording' => true,
                ]
            );

            $studentJoinUrl = $this->jitsi->getJoinUrl(
                $meetingID,
                $booking->student->name,
                false, // not moderator
                [
                    'email' => $booking->student->email,
                    'restrictGuests' => true,
                ]
            );

            $booking->update([
                'status'               => 'confirmed',
                'meeting_room'         => $meetingID,
                'meeting_url'          => $studentJoinUrl,
                'meeting_password'     => null, // Jitsi doesn't use passwords
                'moderator_password'   => null, // Jitsi uses moderator flag instead
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
