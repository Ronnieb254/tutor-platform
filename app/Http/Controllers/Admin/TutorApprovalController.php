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
    /** Approve a tutor profile → make it live */
    public function approve(TutorProfile $profile)
    {
        $profile->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        // Notify tutor
        $profile->user->notify(new \App\Notifications\TutorApproved($profile));

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

        // Check if all docs are now approved — auto-approve profile
        $profile = $document->tutorProfile;
        $allApproved = $profile->documents()->where('status', '!=', 'approved')->doesntExist()
            && $profile->documents()->count() >= 5;

        if ($allApproved) {
            $profile->update(['is_approved' => true, 'approved_at' => now()]);
            try { $profile->user->notify(new \App\Notifications\TutorApproved($profile)); } catch (\Exception $e) {}
            return back()->with('success', 'Document approved. All documents verified — tutor profile is now live!');
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
