# Implementation Plan: Admin Tutor Email Functionality

## Summary
Add email capability to the admin Tutor Approvals page allowing admins to send individual or broadcast emails to tutors from support@ovaldesk.co.ke.

---

## Implementation Plan

- [ ] 1. Create TutorEmailController with two methods for sending emails.
      The controller will validate email subject (min 5 chars) and message (min 20 chars).
      Files: app/Http/Controllers/Admin/TutorEmailController.php
      Verify: `php artisan make:controller Admin/TutorEmailController` runs without error; file exists at correct path.

- [ ] 2. Implement sendToTutor() method: POST handler that emails a single tutor.
      Accepts tutor profile ID via route model binding, validates request, sends email via Mail::send() using inline HTML template.
      Returns JSON: `{"success": true, "message": "Email sent to..."}` or `{"success": false, "errors": {...}}`.
      Files: app/Http/Controllers/Admin/TutorEmailController.php
      Verify: Method signature confirmed; JSON return structure follows Laravel conventions.

- [ ] 3. Implement sendToAll() method: POST handler that emails all approved+pending tutors.
      Queries TutorProfile::all()->with('user'), validates request, sends batch emails.
      Returns JSON with recipient count and success status.
      Files: app/Http/Controllers/Admin/TutorEmailController.php
      Verify: Method iterates over tutors and sends to each; JSON response includes count.

- [ ] 4. Create AdminTutorMail Mailable class for structured email composition.
      Accepts subject, message body, recipient name+email. Uses markdown view for template.
      Files: app/Mail/AdminTutorMail.php
      Verify: File created; implements Mailable interface; has public $subject and $message properties.

- [ ] 5. Create email template matching Mwalimu brand style.
      Blade template with dark green header (#15281F), blue accent (#0A64BC), cream background (#FBF5E6).
      Uses inline table-based HTML, displays subject, message, sender info (Mwalimu Support).
      Files: resources/views/emails/admin-tutor-message.blade.php
      Verify: Template loads without Blade errors; email preview renders in browser as inline HTML.

- [ ] 6. Add two new routes to admin group in web.php.
      Route 1: POST /admin/tutors/{tutorProfile}/send-email → TutorEmailController@sendToTutor, named admin.tutors.send-email.
      Route 2: POST /admin/tutors/send-email-all → TutorEmailController@sendToAll, named admin.tutors.send-email-all.
      Both inside Route::middleware('role:admin')->prefix('admin')->name('admin.')->group().
      Files: routes/web.php
      Verify: `php artisan route:list | grep admin.tutors.send` shows both routes; routes use correct HTTP verbs.

- [ ] 7. Add "Email" button to each tutor card in tutors.blade.php.
      Placed next to "Review" button in card actions div, 12px font, 10px 14px padding, 9px border-radius.
      Button onclick: `openEmailModal({{ $profile->id }}, '{{ $profile->user->name }}', '{{ $profile->user->email }}')`
      Files: resources/views/pages/admin/tutors.blade.php
      Verify: Button renders on card; visually aligns with existing buttons; onclick function name matches JS implementation.

- [ ] 8. Add "Email All Tutors" button to section header (next to "Back to Pending" button).
      Visible only when NOT on approved tutors view ($showApproved === false).
      Button style matches existing header buttons; onclick: `openEmailAllModal()`
      Files: resources/views/pages/admin/tutors.blade.php
      Verify: Button appears in header div; onclick function name matches JS implementation.

- [ ] 9. Implement openEmailModal(profileId, name, email) JavaScript function.
      Opens overlay div with ID `#email-modal-single`.
      Pre-fills: To field with "name (email)", Subject input placeholder, Message textarea.
      Clears form on open; stores profileId in data attribute of submit button.
      Files: resources/views/pages/admin/tutors.blade.php (inside <script> tag before closing @endsection)
      Verify: Function exists in global scope; opens/closes modal overlay correctly; form fields reset on open.

- [ ] 10. Implement openEmailAllModal() JavaScript function.
       Opens overlay div with ID `#email-modal-all`.
       Displays count of recipient tutors (all approved + pending) in modal copy.
       Clears form fields on open.
       Files: resources/views/pages/admin/tutors.blade.php (inside <script> tag)
       Verify: Function exists in global scope; modal opens with correct recipient count; form clears.

- [ ] 11. Implement submitEmail(formId, url) JavaScript function.
       Triggered on form submit in both email modals.
       Prevents default form submission; validates subject length >= 5, message length >= 20.
       Shows loading state (disabled submit button + spinner); sends fetch POST with CSRF token in headers.
       On success: shows toast notification, closes modal, clears form.
       On error: shows error toast with response message.
       Files: resources/views/pages/admin/tutors.blade.php (inside <script> tag)
       Verify: Form validation logic checks lengths correctly; fetch includes CSRF token; toast function exists.

- [ ] 12. Create two email modals in tutors.blade.php: single tutor and all tutors.
       Both modals have: overlay container, modal box, header with close button, form with Subject+Message, Send+Cancel buttons.
       Single-tutor modal (ID: `email-modal-single`): To field (read-only, pre-filled), Subject, Message, character counter for message.
       All-tutors modal (ID: `email-modal-all`): copy showing recipient count, Subject, Message, character counter.
       Both modals hidden by default; onclick on overlay/close button closes modal.
       Files: resources/views/pages/admin/tutors.blade.php (before closing </div> tag)
       Verify: Modals render correctly; form inputs have correct names (subject, message); close buttons work.

- [ ] 13. Add CSRF token handling and toast notification support to JavaScript.
       Extract CSRF token from meta tag or request header for fetch POST requests.
       Implement showToast(message, type) function (or use existing if available) to display success/error notifications.
       Toast auto-hides after 4 seconds; supports type: 'success', 'error'.
       Files: resources/views/pages/admin/tutors.blade.php (inside <script> tag)
       Verify: Fetch requests include CSRF token in X-CSRF-TOKEN header; toast appears on submit success/error.

- [ ] 14. Test email sending via admin panel.
       Navigate to /admin/tutors, click Email button on any tutor card, fill form (subject ≥5 chars, message ≥20 chars), submit.
       Verify: POST request succeeds (check network tab), toast shows "Email sent to [name]", modal closes, form clears.
       Test "Email All Tutors": button appears, modal shows correct count, submit sends batch email, toast confirms.
       Verify send-to-all route sends emails to all tutors.
       Files: N/A (manual testing in browser)
       Verify: Network tab shows POST to /admin/tutors/{id}/send-email or /admin/tutors/send-email-all; response 200 with JSON success message.

- [ ] 15. Verify app/Mail directory created and Mailable class uses correct from() address.
       AdminTutorMail mailable should use from('support@ovaldesk.co.ke', 'Mwalimu Support').
       Email sends with correct From: header in test mailer logs or mail capture.
       Files: app/Mail/AdminTutorMail.php
       Verify: `php artisan tinker` and test: `\App\Mail\AdminTutorMail::class` loads without error; inspect mail log for From header.

---

## Key Design Decisions

1. **Separate Controller**: TutorEmailController stays focused on admin email actions, distinct from approval logic.
2. **Mailable Class**: Uses a dedicated AdminTutorMail class (not Mail::raw) for reusability and cleaner templates.
3. **Inline Email Template**: Matches booking-confirmed-tutor.blade.php styling (Mwalimu brand colors, table-based HTML, inline CSS).
4. **Validation**: Client-side (JS) + server-side (Controller) validation prevents empty/too-short emails.
5. **Modal Pattern**: Two overlays (single + all) reuse existing modal styling from reject-doc modal for consistency.
6. **AJAX Submission**: fetch() with JSON response allows smooth UX without page reload.
7. **Batch Sending**: sendToAll() iterates over tutors; not a queue job (keeps it synchronous for fast feedback).

---

## Files to Create/Modify

**Create:**
- app/Http/Controllers/Admin/TutorEmailController.php
- app/Mail/AdminTutorMail.php
- resources/views/emails/admin-tutor-message.blade.php

**Modify:**
- routes/web.php (add 2 POST routes inside admin group)
- resources/views/pages/admin/tutors.blade.php (add buttons, modals, JS functions)

---

## Testing Checklist

- [ ] Controller methods return correct JSON response structure
- [ ] Email template renders without errors and displays Mwalimu branding
- [ ] Routes are registered and accessible to admin-role users only
- [ ] Email buttons appear on tutor cards and "Email All" in header
- [ ] Modals open/close correctly and forms reset
- [ ] Form validation blocks submission if subject < 5 chars or message < 20 chars
- [ ] Fetch POST succeeds with CSRF token; emails sent with support@ovaldesk.co.ke From: header
- [ ] Toast notifications show success/error messages
- [ ] Batch send reaches all approved + pending tutors
