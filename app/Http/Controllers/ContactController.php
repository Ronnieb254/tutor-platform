<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|min:10',
        ]);

        // Log the contact message (email sending requires mail config)
        \Illuminate\Support\Facades\Log::info('Contact form submission', [
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'role'    => $request->role,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Try to send email if configured
        try {
            Mail::raw(
                "Name: {$request->name}\nEmail: {$request->email}\nPhone: {$request->phone}\nRole: {$request->role}\nSubject: {$request->subject}\n\nMessage:\n{$request->message}",
                function ($msg) use ($request) {
                    $msg->to('info@ovaldesk.co.ke')
                        ->subject("Mwalimu Contact: {$request->subject ?: 'General Enquiry'}")
                        ->replyTo($request->email, $request->name);
                }
            );
        } catch (\Exception $e) {
            // Silently fail if mail not configured
        }

        return back()->with('contact_success', true);
    }
}
