<?php

namespace App\Http\Controllers;

use App\Models\TutorContractAcceptance;
use App\Notifications\TutorContractEmail;
use App\Notifications\TutorContractAcceptedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TutorContractController extends Controller
{
    /**
     * Show the contract acceptance page
     */
    public function show()
    {
        $user = auth()->user();
        
        // If already accepted, redirect to dashboard
        if ($user->contract_accepted) {
            return redirect()->route('tutor.dashboard');
        }
        
        return view('pages.tutor-contract');
    }

    /**
     * Accept the contract
     */
    public function accept(Request $request)
    {
        $request->validate([
            'accept_contract' => 'required|accepted',
        ]);

        $user = auth()->user();
        
        // Check if already accepted
        if ($user->contract_accepted) {
            return redirect()->route('tutor.dashboard')
                ->with('info', 'You have already accepted the contract.');
        }

        // Get contract content hash
        $contractPath = storage_path('app/contracts/tutor-agreement.pdf');
        $contractHash = file_exists($contractPath) 
            ? TutorContractAcceptance::generateContractHash(file_get_contents($contractPath))
            : TutorContractAcceptance::generateContractHash('v1.0-default');

        // Record acceptance
        TutorContractAcceptance::create([
            'user_id' => $user->id,
            'contract_version' => 'v1.0',
            'contract_hash' => $contractHash,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accepted_at' => now(),
        ]);

        // Update user record
        $user->update([
            'contract_accepted' => true,
            'contract_accepted_at' => now(),
        ]);

        // Send acceptance confirmation email
        try {
            $user->notify(new TutorContractAcceptedEmail());
        } catch (\Exception $e) {
            \Log::warning('Failed to send contract acceptance email: ' . $e->getMessage());
        }

        return redirect()->route('tutor.dashboard')
            ->with('success', 'Contract accepted successfully! Welcome to Mwalimu.');
    }

    /**
     * Download the contract PDF
     */
    public function download()
    {
        $contractPath = storage_path('app/contracts/tutor-agreement.pdf');
        
        if (!file_exists($contractPath)) {
            abort(404, 'Contract file not found.');
        }

        return response()->download(
            $contractPath,
            'Mwalimu-Tutor-Services-Agreement.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Resend contract email to a tutor
     */
    public function resend()
    {
        $user = auth()->user();
        
        if ($user->role !== 'tutor') {
            abort(403, 'Only tutors can request contract emails.');
        }

        try {
            $user->notify(new TutorContractEmail());
            
            return back()->with('success', 'Contract email has been resent to your email address.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email. Please try again or contact support.');
        }
    }
}
