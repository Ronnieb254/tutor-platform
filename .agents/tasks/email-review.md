# Admin tutor email messaging: per-tutor and broadcast

Admins can now send templated emails to individual tutors or broadcast messages to all tutors from the Tutor Approvals page. The feature adds an Email button on each tutor card, an Email All button in the section header, and two POST endpoints that validate input, handle errors, and send mail via the `AdminTutorMail` mailable from `support@ovaldesk.co.ke`. Modal UX includes subject/message inputs, character counters, and toast notifications on success or error.

Watch for: The feature is functionally complete and security-sound, with one asymmetry in the route order that's worth noting in review but not blocking.

**Verdict**: APPROVED

---

## High-level view

Email input validation is tight: subject (min 5 chars, max 200), message (min 20 chars, max 5000). The controller catches and returns exceptions as JSON errors, so failures surface in the UI without crashes. Both endpoints lean on Laravel's existing `web` middleware group for CSRF protection; the JS fetch correctly adds the `X-CSRF-TOKEN` header. The single-tutor route parameter is `TutorProfile $profile`, which implicitly guards the email against any ID that doesn't exist.

The email template is clean: a dark header with Mwalimu branding, cream background, and escaped body content using `nl2br(e())` to preserve line breaks while preventing XSS. Character counter and disabled submit state provide feedback during composition.

Route ordering places `send-email-all` before the parameterized `{tutorProfile}/send-email` route, avoiding collisions. The `Email All` button only shows on the pending view, not on approved tutors, which makes sense for the review flow.

<details>
<summary>Issues (1)</summary>

1. **Route ordering asymmetry** — The route for sending to all tutors loads all tutors every request via `TutorProfile::with('user')->get()` without pagination or filtering, which could be slow at scale or if the query is called repeatedly. Not a blocking concern at typical adoption, but queued mail + pagination is a known pattern to follow as the tutor base grows.

</details>

<details>
<summary>Details</summary>

### CSRF and client-side token delivery

Both POST routes sit in the admin middleware group, which applies Laravel's `VerifyCsrfToken` middleware automatically. The JS fetch includes `'X-CSRF-TOKEN': getCsrfToken()` in the request headers, which is the correct pattern for AJAX requests. The `getCsrfToken()` function checks both `meta[name="csrf-token"]` and `input[name="_token"]`, so it falls back gracefully if the meta tag is missing. **Confirmed**: CSRF protection is properly wired.

### Input validation and error handling

The controller validates subject (min 5, max 200) and message (min 20, max 5000) via `Request::validate()`. The modal enforces the same minimums before submission (`showToast('Subject must be at least 5 characters', 'error')`). Both endpoints wrap the mail send in a try-catch that returns a JSON response with `success: false` and the exception message, so any email failure (DNS, SMTP, invalid recipient) is caught and surfaced to the user. The modal then dismisses on success after a 500ms delay, giving the user time to see the success toast. **Confirmed**: validation and error handling are present and consistent.

### Email template safety and brand consistency

The Blade template uses `nl2br(e($body))` to escape the message body and render newlines. `e()` prevents XSS via admin-provided input. The template matches the Mwalimu brand: dark header (`#15281F`), cream background (`#FBF5E6`), centered Mwalimu logo, and white body. The footer includes a copyright notice and "from Mwalimu admin team" disclaimer. **Confirmed**: template is safe and on-brand.

### Mailable configuration and sender address

`AdminTutorMail` sets `from: 'support@ovaldesk.co.ke'` and `name: 'Mwalimu Support'` in the envelope. The constructor accepts `$subject`, `$body`, and an optional `$fromName` (defaulting to 'Mwalimu Support'). Both controller endpoints pass `new AdminTutorMail($validated['subject'], $validated['message'])`, so the sender is always correct. **Confirmed**: mails originate from the required address.

### Route structure and parameter binding

Routes are:
- `POST /admin/tutors/send-email-all` → `TutorEmailController::sendToAll()`
- `POST /admin/tutors/{tutorProfile}/send-email` → `TutorEmailController::sendToTutor()`

The `send-email-all` route is defined first, so it matches literal `/admin/tutors/send-email-all` before the parameter route tries to parse `send-email-all` as a tutor profile ID. The parameterized route uses implicit route model binding (`TutorProfile $profile`), so invalid IDs 404 instead of passing to the controller. **Confirmed**: route order is correct.

### UI affordances and character counter

The modal displays both email/all-tutors variants via `openEmailModal()` and `openEmailAllModal()`. The character counter (`oninput` event on textarea) updates a `<span>` showing current length up to 5000, and character-by-character validation prevents subject/message from being empty or below the min length before submit. Loading state disables the button and shows a spinner, then restores the original label on success or error. Modals use class-based visibility (`classList.add('open')` / `classList.remove('open')`) matching existing patterns in the file. **Confirmed**: UI feedback is complete.

### Broadcast performance caveat

The `sendToAll()` endpoint queries `TutorProfile::with('user')->get()` without pagination, then synchronously iterates and sends mail to each recipient. For 1–100 tutors this is fine, but beyond that, the endpoint could timeout or spike database/mail queue load. At scale, this should use queued jobs and chunked queries. The current implementation works for the immediate use case and is not a blocking concern, but it's a known growth point. **Likely**: no immediate issue; flagged for future optimization.

### Code quality and consistency

`TutorEmailController` follows the same structure as other admin controllers in the codebase: try-catch wrapping business logic, returning JSON responses, and validating input. No unused imports. The `AdminTutorMail` mailable follows Laravel conventions: use of `Queueable` and `SerializesModels` traits for queue compatibility, explicit `Envelope`, and `Content` definitions. Imports are clean. **Confirmed**: code matches project patterns.

</details>

---

## File map

<details>
<summary>Files changed</summary>

- **TutorEmailController.php** — New controller with two methods: `sendToTutor()` sends to a single profile, `sendToAll()` sends to all tutors in the database. Both validate input and handle exceptions.
- **AdminTutorMail.php** — New mailable class. Configures sender (`support@ovaldesk.co.ke`), subject, and body; renders the email template with escaped content.
- **admin-tutor-message.blade.php** — New email template. Dark header with Mwalimu branding, cream background, message body with line breaks, footer disclaimer.
- **tutors.blade.php** — Tutor Approvals page. Added: Email button on each card in the Actions section (calls `openEmailModal()`), Email All button in section header (calls `openEmailAllModal()`), two modals with form validation, character counter, and toast notifications. Added JS functions for modal control, CSRF token retrieval, form submission, and toast display.
- **web.php** — Routes. Added two POST routes under the admin group: `/admin/tutors/send-email-all` and `/admin/tutors/{tutorProfile}/send-email`.

Full diff available via `git diff main`.

</details>
