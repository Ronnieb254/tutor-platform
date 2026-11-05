<?php

namespace App\Http\Controllers;

use App\Models\TutorProfile;
use App\Models\TutorDocument;
use App\Models\Wallet;
use App\Services\DarajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TutorOnboardingController extends Controller
{
    public function submit(Request $request, DarajaService $daraja)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'city'             => 'nullable|string|max:100',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'bio'              => 'required|string|min:50',
            'hourly_rate'      => 'required|numeric|min:500',
            'subjects'         => 'required|array|min:1|max:2',
            'subjects.*'       => 'exists:subjects,id',
            'levels'           => 'nullable|array',
            'speedy'           => 'nullable',
            'curriculum_ids'   => 'nullable|array',
            'curriculum_ids.*' => 'exists:curricula,id',
            // Documents
            'doc_national_id'                  => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_degree_certificate'           => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_tsc_certificate'              => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_certificate_of_good_conduct'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_passport_photo'               => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user    = auth()->user();
        $isSpecedy = filter_var($request->input('speedy'), FILTER_VALIDATE_BOOLEAN);

        // Update user name/phone
        $user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        // Create or update TutorProfile
        $profile = TutorProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'bio'                      => $request->bio,
                'hourly_rate'              => $request->hourly_rate,
                'experience_years'         => $request->experience_years ?? 0,
                'city'                     => $request->city,
                'country'                  => 'Kenya',
                'languages'                => ['English', 'Kiswahili'],
                'is_approved'              => false,
                'speedy_verification_paid' => false,
                'level'                    => 'all',
            ]
        );

        // Sync subjects
        $profile->subjects()->sync($request->subjects);

        // Sync curricula (save to tutor_curricula pivot with pending status)
        if ($request->has('curriculum_ids') && is_array($request->curriculum_ids)) {
            $curriculaSyncData = [];
            foreach ($request->curriculum_ids as $currId) {
                $curriculaSyncData[(int)$currId] = [
                    'verification_status' => 'pending',
                    'verified_at'         => null,
                ];
            }
            $profile->curricula()->sync($curriculaSyncData);
        }

        // Store documents
        $docTypes = [
            'doc_national_id'                 => 'national_id',
            'doc_degree_certificate'          => 'degree_certificate',
            'doc_tsc_certificate'             => 'tsc_certificate',
            'doc_certificate_of_good_conduct' => 'certificate_of_good_conduct',
            'doc_passport_photo'              => 'passport_photo',
        ];

        foreach ($docTypes as $field => $type) {
            if ($request->hasFile($field)) {
                // Delete old doc of same type
                $old = TutorDocument::where('tutor_profile_id', $profile->id)
                    ->where('type', $type)->first();
                if ($old) {
                    Storage::disk('public')->delete($old->file_path);
                    $old->delete();
                }

                $path = $request->file($field)->store(
                    "documents/{$user->id}", 'public'
                );

                TutorDocument::create([
                    'tutor_profile_id' => $profile->id,
                    'type'             => $type,
                    'file_path'        => $path,
                    'original_name'    => $request->file($field)->getClientOriginalName(),
                    'status'           => 'pending',
                ]);
            }
        }

        // Create wallet
        Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'currency' => 'KES']
        );

        // If speedy verification requested, send STK Push
        if ($isSpecedy) {
            $phone     = $request->input('phone', '');
            $reference = $daraja->generateReference('SPV');

            try {
                $result       = $daraja->initializeStkPush($phone, 1000.00, $reference, 'Mwalimu SpeedyVerify');
                $responseCode = $result['ResponseCode'] ?? ($result['errorCode'] ?? null);

                if ($responseCode === '0' || isset($result['_mock'])) {
                    // Store a pending payment so DarajaCallbackController can mark speedy later
                    \App\Models\Payment::create([
                        'user_id'             => $user->id,
                        'amount'              => 1000.00,
                        'currency'            => 'KES',
                        'payment_type'        => 'speedy_verification',
                        'mpesa_reference'     => $reference,
                        'checkout_request_id' => $result['CheckoutRequestID'] ?? null,
                        'payer_phone'         => $daraja->normalizePhone($phone),
                        'status'              => 'pending',
                    ]);

                    return response()->json([
                        'status'   => 'ok',
                        'speedy'   => true,
                        'stk_sent' => true,
                        'tutor_id' => $profile->id,
                        'message'  => $result['CustomerMessage'] ?? 'Check your phone for the M-Pesa prompt.',
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Speedy verification STK failed: ' . $e->getMessage());
                // Fall through — still submitted, just not speedy
            }
        }

        return response()->json([
            'status'  => 'ok',
            'speedy'  => false,
            'tutor_id'=> $profile->id,
        ]);
    }
}
