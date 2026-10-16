<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TutorProfile;
use App\Models\User;
use App\Models\Subject;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SampleBookingSeeder extends Seeder
{
    public function run(): void
    {
        // Get demo users
        $students = User::whereIn('email', ['alice@example.com','sarah@example.com'])->get();
        $tutors   = User::where('role','tutor')->with('tutorProfile')->take(4)->get();

        if ($students->isEmpty() || $tutors->isEmpty()) {
            $this->command->warn('Run StudentSeeder and TutorSeeder first.');
            return;
        }

        $subjects = Subject::all();

        $bookingData = [
            // ── Upcoming confirmed sessions (with session codes) ──
            [
                'student'  => $students->firstWhere('email','alice@example.com'),
                'tutor'    => $tutors[0],
                'minutes'  => 30,
                'offset'   => '+2 hours',   // starts in 2hrs
                'status'   => 'confirmed',
                'paid'     => true,
                'subject'  => 'Mathematics Alternative A',
                'notes'    => 'Need help with quadratic equations and completing the square.',
            ],
            [
                'student'  => $students->firstWhere('email','sarah@example.com'),
                'tutor'    => $tutors[1] ?? $tutors[0],
                'minutes'  => 45,
                'offset'   => '+1 day',
                'status'   => 'confirmed',
                'paid'     => true,
                'subject'  => 'English',
                'notes'    => 'Comprehension and essay writing for KCSE prep.',
            ],
            [
                'student'  => $students->firstWhere('email','alice@example.com'),
                'tutor'    => $tutors[2] ?? $tutors[0],
                'minutes'  => 60,
                'offset'   => '+3 days',
                'status'   => 'confirmed',
                'paid'     => true,
                'subject'  => 'Chemistry',
                'notes'    => 'Organic chemistry — naming compounds and reaction mechanisms.',
            ],
            // ── Free 15-min intro session (upcoming) ──
            [
                'student'  => $students->firstWhere('email','alice@example.com'),
                'tutor'    => $tutors[3] ?? $tutors[0],
                'minutes'  => 15,
                'offset'   => '+4 hours',
                'status'   => 'confirmed',
                'paid'     => false,  // FREE
                'subject'  => 'Physics',
                'notes'    => 'Free intro session — checking compatibility before booking a full session.',
                'free'     => true,
            ],
            // ── Live NOW session (starts -5 min ago) ──
            [
                'student'  => $students->firstWhere('email','sarah@example.com'),
                'tutor'    => $tutors[0],
                'minutes'  => 30,
                'offset'   => '-5 minutes',
                'status'   => 'confirmed',
                'paid'     => true,
                'subject'  => 'Mathematics Alternative A',
                'notes'    => 'Revision session — past paper questions.',
            ],
            // ── Completed sessions ──
            [
                'student'  => $students->firstWhere('email','alice@example.com'),
                'tutor'    => $tutors[0],
                'minutes'  => 45,
                'offset'   => '-2 days',
                'status'   => 'completed',
                'paid'     => true,
                'subject'  => 'Mathematics Alternative A',
                'rating'   => 5,
                'review'   => 'Excellent session! Very clear explanations and patient with questions.',
            ],
            [
                'student'  => $students->firstWhere('email','sarah@example.com'),
                'tutor'    => $tutors[1] ?? $tutors[0],
                'minutes'  => 60,
                'offset'   => '-5 days',
                'status'   => 'completed',
                'paid'     => true,
                'subject'  => 'English',
                'rating'   => 5,
                'review'   => 'My daughter improved so much after just one session. Highly recommend!',
            ],
            [
                'student'  => $students->firstWhere('email','alice@example.com'),
                'tutor'    => $tutors[2] ?? $tutors[0],
                'minutes'  => 30,
                'offset'   => '-1 week',
                'status'   => 'completed',
                'paid'     => true,
                'subject'  => 'Chemistry',
                'rating'   => 4,
                'review'   => 'Good session. Covered a lot of ground on organic chemistry.',
            ],
            // ── Pending ──
            [
                'student'  => $students->firstWhere('email','alice@example.com'),
                'tutor'    => $tutors[3] ?? $tutors[0],
                'minutes'  => 60,
                'offset'   => '+6 days',
                'status'   => 'pending',
                'paid'     => false,
                'subject'  => 'Physics',
                'notes'    => 'Deep dive — mechanics and kinematics.',
            ],
        ];

        $prices = [15 => 0, 30 => 500, 45 => 680, 60 => 800]; // 15min always free

        foreach ($bookingData as $data) {
            $student     = $data['student'];
            $tutorUser   = $data['tutor'];
            $profile     = $tutorUser->tutorProfile;
            if (!$student || !$profile) continue;

            $isFree    = ($data['free'] ?? false) || $data['minutes'] === 15;
            $amount    = $isFree ? 0.00 : ($prices[$data['minutes']] ?? 500);
            $platFee   = $isFree ? 0.00 : round($amount * 0.15, 2);
            $tutorEarn = $isFree ? 0.00 : round($amount - $platFee, 2);

            $subjectId = null;
            if (!empty($data['subject'])) {
                $subj = Subject::where('name', 'like', '%'.$data['subject'].'%')->first();
                $subjectId = $subj?->id;
            }

            $scheduledAt = Carbon::parse('now '.$data['offset']);

            $uuid        = (string) Str::uuid();
            $tutorCode   = 'TUT-' . strtoupper(Str::random(6));
            $studentCode = 'STU-' . strtoupper(Str::random(6));
            $roomName    = 'mwalimu-session-' . $uuid;

            $booking = Booking::create([
                'uuid'                 => $uuid,
                'student_id'           => $student->id,
                'tutor_id'             => $tutorUser->id,
                'subject_id'           => $subjectId,
                'scheduled_at'         => $scheduledAt,
                'duration_minutes'     => $data['minutes'],
                'status'               => $data['status'],
                'meeting_room'         => $roomName,
                'meeting_url'          => 'https://meet.jit.si/'.$roomName,
                'total_amount'         => $amount,
                'platform_fee'         => $platFee,
                'tutor_earning'        => $tutorEarn,
                'student_notes'        => $data['notes'] ?? null,
                'session_code_tutor'   => $data['status'] === 'confirmed' ? $tutorCode : null,
                'session_code_student' => $data['status'] === 'confirmed' ? $studentCode : null,
                'student_joined'       => false,
                'tutor_joined'         => false,
                'completed_at'         => $data['status'] === 'completed' ? $scheduledAt->copy()->addMinutes($data['minutes']) : null,
            ]);

            // Create payment for paid bookings
            if ($data['paid'] && $amount > 0) {
                Payment::create([
                    'booking_id'         => $booking->id,
                    'user_id'            => $student->id,
                    'amount'             => $amount,
                    'currency'           => 'KES',
                    'paystack_reference' => 'SAMPLE-' . strtoupper(Str::random(10)),
                    'status'             => 'success',
                    'paid_at'            => now()->subMinutes(rand(10,60)),
                    'metadata'           => ['sample' => true],
                ]);

                // Credit tutor wallet for completed sessions
                if ($data['status'] === 'completed' && $tutorEarn > 0) {
                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $tutorUser->id],
                        ['balance' => 0, 'currency' => 'KES']
                    );
                    $wallet->credit($tutorEarn, 'Earnings from session #'.substr($uuid,0,8), $uuid);
                    if ($profile) $profile->increment('total_sessions');
                }
            }

            // Add review for completed sessions
            if ($data['status'] === 'completed' && !empty($data['rating'])) {
                $booking->review()->create([
                    'student_id' => $student->id,
                    'tutor_id'   => $tutorUser->id,
                    'rating'     => $data['rating'],
                    'comment'    => $data['review'] ?? null,
                    'is_visible' => true,
                ]);
                // Update tutor rating
                if ($profile) {
                    $avg = $profile->reviews()->where('is_visible',true)->avg('rating');
                    $profile->update([
                        'rating_avg'    => round($avg, 2),
                        'total_reviews' => $profile->reviews()->where('is_visible',true)->count(),
                    ]);
                }
            }

            $label = $isFree ? 'FREE 15min' : 'KSh '.$amount;
            $this->command->info("  ✅ [{$data['status']}] {$student->name} → {$tutorUser->name} | {$data['minutes']}min | {$label} | {$scheduledAt->format('M j H:i')}");
        }

        $this->command->info("\n✅ Sample bookings seeded successfully.");
    }
}
