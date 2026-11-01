<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTutorContractAccepted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only check for tutors
        if ($user && $user->role === 'tutor') {
            // If contract not accepted, redirect to contract page
            if (!$user->contract_accepted) {
                // Allow access to contract-related routes and logout
                $allowedRoutes = [
                    'tutor-contract',
                    'accept-tutor-contract',
                    'download-tutor-contract',
                    'resend-tutor-contract',
                    'logout',
                ];

                $currentRoute = $request->route()?->getName();

                if (!in_array($currentRoute, $allowedRoutes)) {
                    return redirect()->route('tutor-contract')
                        ->with('info', 'Please read and accept the Tutor Services Agreement to continue.');
                }
            }
        }

        return $next($request);
    }
}
