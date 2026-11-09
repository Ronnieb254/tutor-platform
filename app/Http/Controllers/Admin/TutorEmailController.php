<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminTutorMail;
use App\Models\TutorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TutorEmailController extends Controller
{
    /**
     * Send email to a single tutor
     */
    public function sendToTutor(Request $request, TutorProfile $profile)
    {
        try {
            $validated = $request->validate([
                'subject' => 'required|string|min:5|max:200',
                'message' => 'required|string|min:20|max:5000',
            ]);

            Mail::to($profile->user->email, $profile->user->name)->send(
                new AdminTutorMail($validated['subject'], $validated['message'])
            );

            return response()->json([
                'success' => true,
                'message' => 'Email sent to ' . $profile->user->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send email to all tutors (approved and pending)
     */
    public function sendToAll(Request $request)
    {
        try {
            $validated = $request->validate([
                'subject' => 'required|string|min:5|max:200',
                'message' => 'required|string|min:20|max:5000',
            ]);

            $tutors = TutorProfile::with('user')->get();
            $count = 0;

            foreach ($tutors as $tutor) {
                if ($tutor->user && $tutor->user->email) {
                    Mail::to($tutor->user->email, $tutor->user->name)->send(
                        new AdminTutorMail($validated['subject'], $validated['message'])
                    );
                    $count++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Email sent to ' . $count . ' tutor' . ($count !== 1 ? 's' : '')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
