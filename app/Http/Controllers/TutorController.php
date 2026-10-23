<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\PayoutRequest;
use App\Models\TutorDocument;
use App\Models\TutorProfile;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TutorController extends Controller
{
    public function uploadDocument(Request $request)
    {
        // All types that match the DB enum in tutor_documents.type
        $allowedTypes = [
            'national_id',
            'tsc_certificate',
            'degree_certificate',
            'passport_photo',
            'certificate_of_good_conduct',
            'other',
        ];

        $request->validate([
            'type'     => 'required|in:' . implode(',', $allowedTypes),
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user    = auth()->user();
        $profile = $user->tutorProfile;

        if (!$profile) {
            return back()->with('error', 'Please complete your tutor profile first.');
        }

        $path = $request->file('document')->store("documents/{$user->id}", 'public');

        // Delete old document of same type
        $old = TutorDocument::where('tutor_profile_id', $profile->id)
            ->where('type', $request->type)->first();
        if ($old) {
            Storage::disk('public')->delete($old->file_path);
            $old->delete();
        }

        TutorDocument::create([
            'tutor_profile_id' => $profile->id,
            'type'             => $request->type,
            'file_path'        => $path,
            'original_name'    => $request->file('document')->getClientOriginalName(),
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Document uploaded successfully. Our team will review it within 24–48 hours.');
    }

    public function updateAvailability(Request $request)
    {
        $profile = auth()->user()->tutorProfile;
        if (!$profile) return back()->with('error', 'Profile not found.');

        $days = $request->input('days', []);

        // Remove all existing
        $profile->availabilities()->delete();

        // Re-create
        foreach ($days as $day => $data) {
            if (!empty($data['active'])) {
                Availability::create([
                    'tutor_profile_id' => $profile->id,
                    'day_of_week'      => (int) $day,
                    'start_time'       => $data['start'] ?? '09:00',
                    'end_time'         => $data['end']   ?? '18:00',
                    'is_active'        => true,
                ]);
            }
        }

        return back()->with('success', 'Availability updated successfully.');
    }

    public function updatePersonal(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city'  => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $user->update(['name' => $request->name, 'phone' => $request->phone]);

        if ($user->tutorProfile) {
            $user->tutorProfile->update(['city' => $request->city]);
        }

        return back()->with('success', 'Personal information updated.');
    }

    public function updateTutorProfile(Request $request)
    {
        $request->validate([
            'bio'              => 'nullable|string|min:20',
            'hourly_rate'      => 'required|numeric|min:500',
            'experience_years' => 'required|integer|min:0',
        ]);

        $profile = auth()->user()->tutorProfile;
        if (!$profile) return back()->with('error', 'Profile not found.');

        $profile->update([
            'bio'              => $request->bio,
            'hourly_rate'      => $request->hourly_rate,
            'experience_years' => $request->experience_years,
        ]);

        return back()->with('success', 'Teaching profile updated.');
    }

    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:500',
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name'   => 'required|string|max:100',
        ]);

        $user   = auth()->user();
        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        // Check no pending request already
        if ($user->payoutRequests()->where('status', 'pending')->exists()) {
            return back()->with('error', 'You already have a pending payout request.');
        }

        PayoutRequest::create([
            'tutor_id'       => $user->id,
            'amount'         => $request->amount,
            'bank_name'      => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name'   => $request->account_name,
            'status'         => 'pending',
        ]);

        // Debit wallet (hold funds)
        $wallet->debit($request->amount, 'Payout request submitted');

        return back()->with('success', 'Payout request submitted. We\'ll process it within 2–3 business days.');
    }

    public function markNotificationsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
