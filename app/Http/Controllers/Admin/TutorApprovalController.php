<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TutorDocument;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TutorApprovalController extends Controller
{
    /** View/download a tutor document */
    public function viewDocument(TutorDocument $document)
    {
        // Check if file exists
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Document not found');
        }

        // Return file for inline viewing or download
        return response()->file($filePath, [
            'Content-Type' => $this->getMimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
        ]);
    }

    /** Get MIME type for file */
    private function getMimeType($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /** Approve a tutor profile → make it live */
    public function approve(TutorProfile $profile)
    {
        $requiredTypes   = ['national_id', 'tsc_certificate', 'degree_certificate', 'passport_photo'];
        $approvedTypes   = $profile->documents()->where('status', 'approved')->pluck('type')->toArray();
        $missingRequired = array_diff($requiredTypes, $approvedTypes);

        // Block if any required doc is missing or not yet approved
        if (!empty($missingRequired)) {
            $missing = implode(', ', array_map(fn($t) => ucwords(str_replace('_', ' ', $t)), $missingRequired));
            return back()->with('error', "Cannot approve — the following required documents are missing or not yet approved: {$missing}.");
        }

        $subjectCount = $profile->subjects()->count();

        // Block if no subjects or more than 2
        if ($subjectCount < 1) {
            try { $profile->user->notify(new \App\Notifications\TutorNeedsSubjects()); } catch (\Exception $e) {}
            return back()->with('info', "{$profile->user->name} has no subjects selected. They have been notified to choose 1–2 subjects before going live.");
        }

        if ($subjectCount > 2) {
            return back()->with('error', "{$profile->user->name} has {$subjectCount} subjects selected. Maximum allowed is 2. Ask them to reduce their selection.");
        }

        $profile->update([
            'is_approved'         => true,
            'approved_at'         => now(),
            'verification_status' => 'approved',
        ]);

        try { $profile->user->notify(new \App\Notifications\TutorApproved($profile)); } catch (\Exception $e) {}

        return back()->with('success', "{$profile->user->name}'s profile has been approved and is now live.");
    }

    /** Suspend / reject a tutor */
    public function suspend(TutorProfile $profile)
    {
        $profile->update(['is_approved' => false, 'approved_at' => null]);

        return back()->with('success', "{$profile->user->name}'s profile has been suspended.");
    }

    /** Approve a single document */
    public function approveDoc(TutorDocument $document)
    {
        $document->update([
            'status'      => 'approved',
            'reviewed_at' => now(),
            'admin_note'  => null,
        ]);

        $profile = $document->tutorProfile;

        // Required doc types (good conduct is optional)
        $requiredTypes = ['national_id', 'tsc_certificate', 'degree_certificate', 'passport_photo'];

        $approvedTypes = $profile->documents()
            ->where('status', 'approved')
            ->pluck('type')
            ->toArray();

        $allRequiredApproved = count(array_diff($requiredTypes, $approvedTypes)) === 0;

        if ($allRequiredApproved && !$profile->is_approved) {
            $subjectCount = $profile->subjects()->count();

            if ($subjectCount < 1) {
                // Docs all approved but no subjects — notify tutor
                try {
                    $profile->user->notify(new \App\Notifications\TutorNeedsSubjects());
                } catch (\Exception $e) {
                    \Log::warning('Failed to send TutorNeedsSubjects notification: ' . $e->getMessage());
                }
                return back()->with('success', 'Document approved. All 4 required documents verified — tutor notified to select 1–2 teaching subjects before going live.');
            }

            if ($subjectCount > 2) {
                return back()->with('success', 'Document approved. All 4 required documents verified — but tutor has ' . $subjectCount . ' subjects (max 2). Admin must ask them to reduce before approving.');
            }

            // All required docs approved + 1–2 subjects → go live
            $profile->update([
                'is_approved'         => true,
                'approved_at'         => now(),
                'verification_status' => 'approved',
            ]);
            try { $profile->user->notify(new \App\Notifications\TutorApproved($profile)); } catch (\Exception $e) {}
            return back()->with('success', 'Document approved. All 4 required documents verified — tutor profile is now live!');
        }

        return back()->with('success', 'Document approved.');
    }

    /** Reject a single document with a reason */
    public function rejectDoc(TutorDocument $document, Request $request)
    {
        $request->validate([
            'reason'     => 'required|string',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $note = $request->reason;
        if ($request->admin_note) $note .= ' — ' . $request->admin_note;

        $document->update([
            'status'      => 'rejected',
            'reviewed_at' => now(),
            'admin_note'  => $note,
        ]);

        // Notify tutor that action is required
        try {
            $document->tutorProfile->user->notify(
                new \App\Notifications\DocumentRejected($document, $note)
            );
        } catch (\Exception $e) {}

        return back()->with('success', 'Document rejected. Tutor has been notified.');
    }

    /** Toggle user active/suspended */
    public function toggleUser(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->fresh()->is_active ? 'activated' : 'suspended';
        return back()->with('success', "{$user->name} has been {$status}.");
    }

    /** Change a user's role — super-admin only */
    public function changeRole(User $user, Request $request)
    {
        // Only super-admin may call this
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only super-admins can change user roles.');
        }

        $request->validate([
            'role' => 'required|in:student,parent,tutor,admin,super-admin',
        ]);

        // Prevent removing own super-admin role
        if ($user->id === auth()->id() && $request->role !== 'super-admin') {
            return back()->withErrors(['role' => 'You cannot demote yourself from super-admin.']);
        }

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);

        \Log::info('User role changed by super-admin', [
            'target_user' => $user->id,
            'old_role'    => $oldRole,
            'new_role'    => $request->role,
            'changed_by'  => auth()->id(),
        ]);

        return back()->with('success', "{$user->name}'s role changed from {$oldRole} to {$request->role}.");
    }
}
