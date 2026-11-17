# Paystack Integration — Investigation Findings

**Goal (verbatim user request):** "comment out manual payment, integrate paystack for the 1 on 1 session bookings, clinics, and group session payment"

**Scope of this report:** Investigation only. No source files were edited. This document maps the current payment system so the implementation step can plan the Paystack integration. All paths are relative to `tutor-platform/`.

---

## 1. Paystack config & environment keys

### `.env` (full read)
- **No `PAYSTACK_*` keys are present and none are even declared.** The file contains:
  - `DARAJA_*` (Safaricom M-Pesa STK Push) — sandbox credentials present and set.
  - `IMBANK_*` (I&M Bank gateway) — declared, all values empty.
  - `INTASEND_*` — commented out.
  - `APP_TIMEZONE=Africa/Nairobi` (relevant to the separate time-fix work, not payment).

### `config/paystack.php` (full read — 9 lines)
```php
return [
    'public_key'  => env('PAYSTACK_PUBLIC_KEY'),
    'secret_key'  => env('PAYSTACK_SECRET_KEY'),
    'payment_url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
    'currency'    => env('PAYSTACK_CURRENCY', 'KES'),
];
```
- **Key names the JS SDK / backend need:**
  - `PAYSTACK_PUBLIC_KEY` — the **public key** used by the Paystack inline JS SDK (`PaystackPop.setup({key: ...})`) and embeddable in the frontend. Currently **unset/empty**.
  - `PAYSTACK_SECRET_KEY` — the **secret key** used server-side only (initialize + verify). Currently **unset/empty**.
  - `PAYSTACK_PAYMENT_URL` — defaults to `https://api.paystack.co`.
  - `PAYSTACK_CURRENCY` — defaults to `KES`.
- **Action required:** add these four keys to `.env` with real Paystack test/live credentials before integration will function.

### Existing Paystack service/helper/controller files (search of `app/`)
Three files exist:
- `app/Services/PaystackService.php`
- `app/Http/Controllers/PaystackWebhookController.php`
- `app/Http/Controllers/PaystackCallbackHelper.php`

(See §2 and §6.)

---

## 2. Existing PaystackService, PaystackWebhookController, PaystackCallbackHelper

### `app/Services/PaystackService.php` (full read)
- Marked `@deprecated — Paystack has been removed. Use DarajaService for all payments.` It is a **no-op stub**:
  - `initializeTransaction(): array { return []; }`
  - `verifyTransaction(): array { return []; }`
  - `verifyWebhookSignature(): bool { return false; }`
  - `generateReference(string $prefix = 'TL'): string` — the only working method; returns `"{prefix}-" + Str::random(12) + "-" + time()`.
- **This stub must be replaced with a real implementation** (HTTP calls to Paystack initialize/verify endpoints using `config('paystack.secret_key')` and `config('paystack.payment_url')`). Note `App\Services\DarajaService` has a `generateReference()` with the identical signature, kept deliberately compatible.

### `app/Http/Controllers/PaystackWebhookController.php` (full read)
- Marked `@deprecated`. `handle()` returns HTTP 410 `'Paystack has been removed. Use /api/daraja/callback.'`
- **Not wired to any route** (confirmed by route grep — see §9). It is dead code.

### `app/Http/Controllers/PaystackCallbackHelper.php` (full read) — **the key reusable asset**
A real, non-deprecated helper with three static methods, designed to confirm whatever session a verified `Payment` belongs to and redirect to the right success page:
- `confirmAndRedirect(Payment $payment): RedirectResponse`
  - If `payment_type` is `null`/`'booking'` or `booking_id` set → calls `BookingService::confirmBooking($booking)`, redirects to `route('bookings.show', $booking->uuid)`.
  - If `payment_type` is `'clinic'` or `'group_session'` → calls `confirmClinicEnrollment($payment)`.
- `redirectAfterPayment(Payment $payment, string $message): RedirectResponse` — used when a webhook already confirmed before the user arrived (idempotent redirect).
- `confirmClinicEnrollment(Payment $payment): RedirectResponse` — creates a `ClinicEnrollment` (with `session_code = Str::random(8)`), auto-creates overflow session if full, redirects to `group-sessions.show` (for group) or `clinic.my-bookings` (for clinic). Capacity + idempotency (`isEnrolledBy`) checks included.
- **Verification approach:** the helper does **not** itself call Paystack's verify API. It assumes the caller has already verified the transaction and updated the `Payment` to `status = 'success'`. So a controller must verify-then-call this helper. (Signature verification is expected to live in `PaystackService::verifyWebhookSignature()` / a verify endpoint.)
- **Wired anywhere?** Grep for `PaystackCallbackHelper` across all `*.php` returns only its own definition. **It is currently orphaned — not called by any controller or route.** It is ready to be wired into a new Paystack callback handler.

---

## 3. 1-on-1 booking payment flow

### `resources/views/pages/tutors/show.blade.php` (full read of payment region + JS)
- **Payment UI section** (inside `@auth`, step 2 of the booking wizard). Exact lines:
  - **M-Pesa Paybill instructions block:** lines **1359–1382** (`{{-- M-Pesa Paybill Instructions --}}` at 1359; the green box with Paybill `542542`, Account `313126`, Account Name "OBARE OKOTH JACOB", and the "Steps: M-Pesa → Lipa na M-Pesa → Pay Bill …" line at 1378). Wrapped in `<div x-show="!isFree">`.
  - **M-Pesa Transaction Code input:** lines **1384–1392** — `<input type="text" x-model="mpesaCode" … maxlength="20">` with helper text.
  - **Submit button:** lines **1393–1406** — `<button @click="processPayment($data)" …>Submit Payment</button>` (free sessions show "Book Free Session").
  - Step 3 (lines ~1422+) shows the pending-verification / confirmed states; pending state tells the user admin verifies "under 5 minutes".
- **`processPayment(data)` JS function:** lines **1601–1636**.
  - Validates `data.date`/`data.time`; if `!data.isFree` requires `data.mpesaCode` ≥ 6 chars.
  - `fetch('/bookings', { method:'POST', … body: JSON.stringify({ tutor_profile_id, duration_minutes, scheduled_at: date+' '+time, subject_id, notes, mpesa_code }) })`.
  - On `res.status==='ok' && res.free` → confirmed (step 3, redirect to `booking_url`).
  - On `res.status==='ok' && res.mode==='manual'` → `window.location.href = res.pending_url`.
- **Current payment method:** **Manual M-Pesa Paybill code entry.** The student pays Paybill 542542 / account 313126 on their own phone, then types the M-Pesa transaction code into the form, which is POSTed to `/bookings`.
- **What needs to change:** comment out the paybill instructions + transaction-code input (lines 1359–1392); replace the `processPayment` body (lines 1601–1636) so that, for paid (non-free) sessions, it **invokes Paystack** (Popup inline SDK or a server init endpoint that returns a Paystack authorization URL) instead of submitting `mpesa_code`. The free-session path should remain unchanged.

### `app/Http/Controllers/BookingController.php` (full read)
- `store(Request)` (the booking creation + payment entry):
  - Validates booking fields; `payment_method` allowed `mpesa,airtel,card` (currently unused downstream).
  - Parses `scheduled_at` as **Africa/Nairobi (EAT) → converts to UTC** before storing (this is the time-fix logic; keep as-is).
  - If `total_amount <= 0` → creates a `Payment` with `status='success'`, `mpesa_reference='FREE-...'`, calls `BookingService::confirmBooking($booking)`, returns JSON `{status:'ok', free:true, booking_uuid, student_code, booking_url}`.
  - **Otherwise (paid):** `validate(['mpesa_code' => 'required|string|min:6|max:30'])`, then creates `Payment` with:
    - `payment_type => 'booking'`, `status => 'pending_verification'`, `mpesa_code => strtoupper(trim(...))`, `metadata => ['payment_instructions'=>'Paybill 542542, Account 313126', 'student_submitted_at'=>...]`.
    - Returns JSON `{status:'ok', mode:'manual', booking_id, booking_uuid, payment_id, message, pending_url => route('bookings.payment-pending', $booking->uuid)}`.
  - **No `mpesa_reference` is set** for manual bookings (unlike the group flow). No Paystack init, no Daraja STK push.
- `paymentPending(Booking)` → view `pages.bookings.payment-pending` (polls `/bookings/payment-status`).
- `paymentStatus(Request)` → returns `{status, booking_uuid, student_code, booking_url, admin_note, verified_at}` for a given `payment_id`.
- `confirm(Request)` → polls by `mpesa_reference` (the Daraja STK path; not used by the current manual flow).
- **What needs to change in `store`:** for paid bookings, drop the `mpesa_code` requirement; instead create the `Payment` with `status='pending'`, a generated reference stored in `mpesa_reference`, and return a Paystack authorization URL (from `PaystackService::initializeTransaction`) or a flag telling the JS to open the Paystack popup. The post-Paystack verification + `BookingService::confirmBooking` should happen in the new Paystack callback/verify handler (reusing `PaystackCallbackHelper::confirmAndRedirect`).

### `routes/web.php` — booking routes (auth group)
- `POST /bookings` → `BookingController@store` — name `bookings.store`
- `POST /bookings/confirm` → `BookingController@confirm` — name `bookings.confirm`
- `GET /bookings/payment-status` → `BookingController@paymentStatus` — name `bookings.payment-status`
- `GET /bookings/{booking}/payment-pending` → `BookingController@paymentPending` — name `bookings.payment-pending` (`whereUuid`)
- `GET /bookings/{booking}` → `BookingController@show` — name `bookings.show`
- `POST /bookings/{booking}/cancel` → `BookingController@cancel` — name `bookings.cancel`
- `POST /bookings/{booking}/force-confirm` → `BookingController@forceConfirm` — name `bookings.force-confirm`
- `GET /api/tutors/{tutor}/availability` → `BookingController@getAvailableSlots` — name `api.tutor.availability`

---

## 4. Clinic payment flow

### `resources/views/pages/clinic-checkout.blade.php` (full read)
- Checkout page reached via `GET /clinic/{clinic}/checkout` (`ClinicBookingController@checkout`). Shows clinic info, order summary (KSh price), and a payment form.
- **Manual payment UI (to comment out):**
  - "Pay via M-Pesa Paybill" heading: line **96**.
  - I&M Bank Paybill instructions box: lines **98–115** (Paybill `542542`, Account `313126`, Account Name "OBARE OKOTH JACOB", steps).
  - **Payment `<form id="paymentForm" method="POST" action="{{ route('clinic.pay', $clinic) }}">`**: lines **119–164**.
    - `@csrf`.
    - M-Pesa Transaction Code `<input type="text" name="mpesa_code" … maxlength="20">`: lines **125–133** + `@error('mpesa_code')` at 133.
    - "Logged in as" box: lines ~135–140.
    - Terms checkbox: lines ~143–148.
    - Submit button "Submit Payment · KSh …": lines ~150–160.
    - Back link: lines ~161–163.
  - The submit handler (bottom script) just disables the button + shows a spinner.
- **Current payment method:** **Manual M-Pesa Paybill code entry.** Form POSTs `mpesa_code` to `clinic.pay`.
- **What needs to change:** comment out lines **96–164** (paybill instructions + the `mpesa_code` form), and replace with a "Pay with Paystack" button that triggers Paystack (popup or server init → redirect). The form action `clinic.pay` and its `mpesa_code` validation become obsolete for the Paystack path.

### `app/Http/Controllers/ClinicBookingController.php` (full read)
- `checkout(Request, ExaminerClinic)` → guards (already-enrolled / full), returns `view('pages.clinic-checkout', ...)`.
- `pay(Request, ExaminerClinic)`:
  - Guards `isFull()` / `isEnrolledBy()`.
  - `validate(['mpesa_code' => 'required|string|min:6|max:30'])`.
  - Creates `Payment` with `payment_type => 'clinic'`, `status => 'pending_verification'`, `mpesa_code`, `metadata => ['clinic_id','clinic_title','payment_instructions'=>'Paybill 542542, Account 313126']`. **No `ClinicEnrollment` is created here** — enrollment is created later by admin verification (`ManualPaymentController@confirmRelatedClinic`) or by `PaystackCallbackHelper::confirmClinicEnrollment`.
  - Redirects to `clinic.my-bookings` with "Admin will verify within 5 minutes…".
- `paymentPending(Request)` → looks up by `mpesa_reference`; if `success` redirects to `clinic.my-bookings`; else view `pages.clinic-payment-pending`. (This is the Daraja STK polling page route `clinic.payment-pending`.)
- **No `callback()` method exists** even though route `clinic.callback` is registered → see §9 latent bug.
- **What needs to change:** replace `pay()`'s `mpesa_code` handling with Paystack init (create `Payment` `status='pending'`, `payment_type='clinic'`, `mpesa_reference`=generated ref, `metadata` with clinic id/title) and redirect to Paystack; on return, the Paystack callback verifies and calls `PaystackCallbackHelper::confirmAndRedirect` (which creates the `ClinicEnrollment`).

### `routes/web.php` — clinic payment routes (auth group)
- `POST /clinic/{clinic}/pay` → `ClinicBookingController@pay` — name `clinic.pay`
- `GET /clinic/callback` → `ClinicBookingController@callback` — name `clinic.callback` **(LATENT BUG: `callback()` method does not exist on the controller — would 500 if hit)**
- `GET /clinic/payment-pending` → `ClinicBookingController@paymentPending` — name `clinic.payment-pending` (declared in a separate auth group ~line 492; the Daraja polling page)
- `GET /clinic/{clinic}/checkout` → `ClinicBookingController@checkout` — name `clinic.checkout`
- `GET /clinic/my-bookings` → `ClinicBookingController@myBookings` — name `clinic.my-bookings`

---

## 5. Group session payment flow

### `resources/views/pages/group-sessions/checkout.blade.php` (full read)
- Checkout page reached via `GET /group-sessions/{groupSession}/checkout` (`GroupSessionController@checkout`). Two-column layout: session details (left) + order summary (right).
- **Manual payment UI (to comment out):** the entire "M-Pesa Payment" block, lines **59–143**:
  - `{{-- M-Pesa Payment Form --}}`: line **59**.
  - M-Pesa payment box `<div style="background:rgba(10,100,188,.02)…">`: line **61**.
  - `<h2>…M-Pesa Payment</h2>`: lines **62–64**.
  - `<form method="POST" action="{{ route('group-sessions.enroll', $groupSession) }}" id="enrollForm">`: line **65** (`@csrf` line 67).
  - Phone `<input type="tel" name="phone" …>`: lines **69–84** (validated `min:9|max:15`).
  - M-Pesa Transaction Code `<input type="text" name="mpesa_code" …>`: lines **86–107** + `@error('mpesa_code')` lines 100–103.
  - "How to pay" steps (M-Pesa menu → Lipa Na M-Pesa Online → Business 542542 → Account 313126 → Amount → PIN): lines **109–127**.
  - "Submit Payment Code" button: lines **129–132**.
  - Cancel link: lines **134–136**; `</form>` line **137**.
  - "Secure process … within 2 hours" notice: lines ~139–143.
- **Current payment method:** **Manual M-Pesa Paybill code entry** (plus a phone field). Form POSTs `phone` + `mpesa_code` to `group-sessions.enroll`.
- **What needs to change:** comment out lines **59–143**; replace with a "Pay with Paystack" button. Phone is no longer needed (Paystack collects the payer's channel).

### `resources/views/pages/group-sessions/payment-pending.blade.php` (full read)
- Static "Seat Reserved / payment under review" page shown after `enroll`. Displays `$groupSession` details + `$reference` (the `mpesa_reference`).
- Messaging says "Your M-Pesa code has been submitted for verification", "Admin Verifies Payment (1-2 hours)", "You Get Session Code".
- **No live polling** (unlike the 1-on-1 page). It is a pure status/confirmation page.
- **What needs to change:** with Paystack (auto-verified on callback), this page's manual-verification messaging should be updated; if the redirect-flow confirms synchronously, users may never land here. Either repurpose it as a "processing" interstitial that polls a status endpoint, or route confirmed payments straight to `group-sessions.show`/`my-sessions`.

### `app/Http/Controllers/GroupSessionController.php` (full read of relevant methods)
- `enroll(Request, ExaminerClinic)`:
  - Guards `is_group_session`, `isEnrolledBy`, `isFull`.
  - `validate(['phone'=>'required|min:9|max:15', 'mpesa_code'=>'required|min:6|max:30'])`.
  - Uses `DarajaService` only for `normalizePhone()` and `generateReference('GRP')` — **no STK push is sent** (so the manual code is the actual payment evidence).
  - Creates `Payment`: `payment_type=>'group_session'`, `mpesa_code`, `mpesa_reference=>$reference`, `payer_phone`, `status=>'pending_verification'`, metadata (session id/title/tutor/submitted_at).
  - **Also creates a provisional `ClinicEnrollment`** (`clinic_id`, `student_id`, `payment_id`, `enrolled_at`, `status=>'pending'`) — note: **no `session_code`** is set on this provisional enrollment.
  - Redirects to `group-sessions.payment-pending` with reference.
- `paymentPending(Request)` → looks up `Payment` by `mpesa_reference`; if `success` → redirect to `my-sessions`; else view `pages.group-sessions.payment-pending`.
- `checkout(ExaminerClinic)` → view `pages.group-sessions.checkout`.
- **What needs to change:** replace `enroll()`'s `phone`/`mpesa_code` handling with Paystack init (create `Payment` `status='pending'`, `payment_type='group_session'`, `mpesa_reference`=ref). **Important integration subtlety:** the current code pre-creates a `ClinicEnrollment` with `status='pending'` and no `session_code`. `PaystackCallbackHelper::confirmClinicEnrollment` checks `$clinic->isEnrolledBy($user)` and, finding the pending enrollment, would return "already enrolled" and **skip creating a real enrollment with a session_code**. The implementation must resolve this: either (a) do NOT pre-create the enrollment in `enroll()` and let the callback create it (consistent with the clinic flow), or (b) on Paystack success, update the existing pending enrollment with a `session_code` and set `status` active instead of creating a new one. Option (a) is simpler and matches the clinic flow + the helper's design.

### `routes/web.php` — group session payment routes
- `GET /group-sessions/{groupSession}/checkout` → `GroupSessionController@checkout` — name `group-sessions.checkout` (`whereNumber`)
- `POST /group-sessions/{groupSession}/enroll` → `GroupSessionController@enroll` — name `group-sessions.enroll` (`whereNumber`)
- `GET /group-sessions/payment-pending/{reference}` → `GroupSessionController@paymentPending` — name `group-sessions.payment-pending` (public, outside auth group)
- `GET /group-sessions/my-sessions` → `GroupSessionController@mySessions` — name `group-sessions.my-sessions`
- `GET /group-sessions/{groupSession}` → `GroupSessionController@show` — name `group-sessions.show`

---

## 6. PaystackCallbackHelper — detail (full read already in §2)

- **Purpose:** post-verification confirmation + redirect for any session type tied to a `Payment`.
- **Methods:** `confirmAndRedirect(Payment)`, `redirectAfterPayment(Payment, message)`, `confirmClinicEnrollment(Payment)`.
- **How it "verifies":** it does **not** verify the Paystack transaction itself; it trusts that the caller already verified and flipped `Payment->status` to `success`. So the new Paystack controller must: receive the Paystack `reference`, call `PaystackService::verifyTransaction($reference)`, confirm `data.status === 'success'`, update the `Payment` (`status='success'`, `paid_at`, store receipt), then call `PaystackCallbackHelper::confirmAndRedirect($payment)`.
- **Signature verification** (for a webhook) is expected in `PaystackService::verifyWebhookSignature()` (currently stubbed `false`).
- **Currently wired?** No — grep confirms `PaystackCallbackHelper` is referenced only by its own file. Orphaned, ready to wire.

---

## 7. Payment model & table schema

### `app/Models/Payment.php` (full read)
- **`$fillable`:** `booking_id, clinic_id, course_id, user_id, amount, currency, payment_type, tutor_amount, platform_amount, commission_rate, mpesa_reference, checkout_request_id, merchant_request_id, mpesa_receipt, payer_phone, mpesa_code, admin_note, verified_by, verified_at, status, failure_reason, metadata, paid_at`
- **Casts:** `amount => decimal:2`, `metadata => array`, `paid_at => datetime`, `verified_at => datetime`.
- **Relationships:** `booking` (Booking), `clinic` (ExaminerClinic), `user` (User), `verifiedBy` (User, `verified_by`).
- **`scopeSuccessful`** → `status = 'success'`.
- **`calculateCommission()`** (auto-run on `creating` if amounts null): `clinic`/`group_session` → 25% tutor / 75% platform; else (booking) → 60% tutor / 40% platform.

### `status` allowed values (from migrations)
- Original enum (`2024_01_01_000006_create_payments_table.php`): `pending, success, failed, refunded`.
- Extended by `2026_11_12_000001_add_manual_payment_fields_to_payments_table.php` via `ALTER TABLE … status ENUM('pending','pending_verification','success','failed','refunded') NOT NULL DEFAULT 'pending'`.
- **Final `status` values:** `pending, pending_verification, success, failed, refunded`.
  - Current manual flows use `pending_verification` (awaiting admin). A Paystack flow should use `pending` (awaiting gateway) → `success`/`failed`.

### `payment_type` values (in use across the codebase)
- `'booking'` — 1-on-1 session (BookingController).
- `'clinic'` — examiner clinic (ClinicBookingController).
- `'group_session'` — group session (GroupSessionController; stored in same `examiner_clinics` table, `is_group_session=1`).
- `'course'` — course enrollment (referenced in `DarajaCallbackController`).
- `null` — legacy bookings (treated as booking by the helper).

### Reference / metadata columns
- **`paystack_reference` was DROPPED** in migration `2026_11_04_000001_migrate_payments_to_daraja.php` (it renamed `paystack_reference → mpesa_reference` and dropped `paystack_access_code`). The down() restores them.
- Current generic reference column = **`mpesa_reference`** (nullable, unique). `checkout_request_id` (Daraja) and `merchant_request_id` also exist.
- I&M Bank columns added later: `imbank_transaction_id`, `imbank_reference`, `imbank_receipt_number` (`2026_11_07_070327`).
- `metadata` is a **JSON column** (cast to array) — ideal for storing Paystack details: `access_code`, `authorization_url`, gateway `channel`, `gateway_response`, payer email, flow type, etc.
- **Where to store a Paystack transaction reference:** reuse **`mpesa_reference`** as the generic reference (it already holds Daraja/GRP references and is what the existing `DarajaCallbackController::status` and group `paymentPending` look up by). Alternatively add a dedicated nullable `paystack_reference` column. **Recommendation: reuse `mpesa_reference`** to avoid a new migration and to stay consistent with the existing status-polling lookup; put Paystack-specific fields (access_code, channel) in `metadata`.

---

## 8. Existing automated-payment pattern (Daraja) — the model Paystack parallels

For context, the codebase already has a complete automated M-Pesa STK-Push flow (currently **not** wired into the three manual checkout flows, but present and working):

- `app/Services/DarajaService.php`: `getAccessToken()`, `initializeStkPush(phone, amount, reference, desc)`, `queryStkStatus(checkoutRequestId)`, `parseCallback(payload)`, `generateReference(prefix)`, `normalizePhone(phone)`, `isSandboxPlaceholder()`.
- `app/Http/Controllers/DarajaCallbackController.php`:
  - `handle(Request)` — receives Safaricom callback, looks up `Payment` by `checkout_request_id`, on success sets `status='success'`, `paid_at`, `mpesa_receipt`, `payer_phone`, then `confirmRelatedRecord()` (booking → `BookingService::confirmBooking`; clinic/group → creates `ClinicEnrollment` + notifies; course → `CourseEnrollment`). Idempotent. Returns Safaricom's expected `{ResultCode:0}`.
  - `status(Request)` — polls by `mpesa_reference`; returns `success`/`failed`/`pending` + redirect URLs.
- Routes (api.php, CSRF-excluded in `bootstrap/app.php`): `POST /api/daraja/callback` (`daraja.callback`), `GET /api/daraja/status` (`daraja.status`).

**This callback+status+confirmRelatedRecord architecture is the direct template for the Paystack callback/verify handler.** `PaystackCallbackHelper` already encapsulates the "confirm + redirect per payment_type" half.

### Admin manual-verification back-office (becomes largely obsolete with Paystack auto-verification)
- `app/Http/Controllers/Admin/ManualPaymentController.php` (full read):
  - `index()` → lists `status='pending_verification'` payments (view `pages.admin.manual-payments`).
  - `verify(Payment)` → sets `status='success'`, `verified_by/at`; `confirmRelatedBooking()` (booking → `BookingService::confirmBooking` + fallback codes + email notifications) and `confirmRelatedClinic()` (creates `ClinicEnrollment` with `session_code='CLI-...'` + `ClinicEnrollmentConfirmed` notification). **This is the current "confirm" mechanism for manual clinic bookings** (clinic `pay()` creates no enrollment).
  - `reject(Payment)` → `status='failed'`, cancels booking, sends `PaymentRejected`.
- `app/Http/Controllers/Admin/PaymentVerificationController.php` — group-session payment verification counterpart (referenced by admin routes `group-session-payments.*`; not read in full). View: `resources/views/admin/payments/pending-group-sessions.blade.php`.
- Admin views: `resources/views/pages/admin/manual-payments.blade.php`, `resources/views/admin/payments/pending-group-sessions.blade.php`.
- These admin pages are back-office; the user's "comment out manual payment" instruction targets the **customer-facing** manual code-entry UI (§3/§4/§5). With Paystack auto-verifying, these admin queues receive no new items, but they need not be removed for this task.

---

## 9. Routes — every payment-related route

### `routes/web.php` (auth group — customer-facing)
| Method | Path | Controller@action | Name | Notes |
|---|---|---|---|---|
| POST | `/bookings` | `BookingController@store` | `bookings.store` | creates booking + Payment (manual) |
| POST | `/bookings/confirm` | `BookingController@confirm` | `bookings.confirm` | polls by `mpesa_reference` (Daraja path) |
| GET | `/bookings/payment-status` | `BookingController@paymentStatus` | `bookings.payment-status` | polls by `payment_id` |
| GET | `/bookings/{booking}/payment-pending` | `BookingController@paymentPending` | `bookings.payment-pending` | `whereUuid` |
| GET | `/bookings/{booking}` | `BookingController@show` | `bookings.show` | `whereUuid` |
| POST | `/bookings/{booking}/force-confirm` | `BookingController@forceConfirm` | `bookings.force-confirm` | admin/tutor |
| POST | `/clinic/{clinic}/pay` | `ClinicBookingController@pay` | `clinic.pay` | manual mpesa_code |
| GET | `/clinic/{clinic}/checkout` | `ClinicBookingController@checkout` | `clinic.checkout` | |
| GET | `/clinic/callback` | `ClinicBookingController@callback` | `clinic.callback` | **BUG: `callback()` method missing** |
| GET | `/clinic/my-bookings` | `ClinicBookingController@myBookings` | `clinic.my-bookings` | |
| GET | `/clinic/payment-pending` | `ClinicBookingController@paymentPending` | `clinic.payment-pending` | separate auth group ~L492 |
| POST | `/group-sessions/{groupSession}/enroll` | `GroupSessionController@enroll` | `group-sessions.enroll` | manual phone+mpesa_code; `whereNumber` |
| GET | `/group-sessions/{groupSession}/checkout` | `GroupSessionController@checkout` | `group-sessions.checkout` | `whereNumber` |
| GET | `/group-sessions/payment-pending/{reference}` | `GroupSessionController@paymentPending` | `group-sessions.payment-pending` | public |
| GET | `/group-sessions/my-sessions` | `GroupSessionController@mySessions` | `group-sessions.my-sessions` | |
| GET | `/group-sessions/{groupSession}` | `GroupSessionController@show` | `group-sessions.show` | `whereNumber` |

### `routes/web.php` (admin group, prefix `admin`, name `admin.*`, `role:admin`)
| Method | Path | Controller@action | Name |
|---|---|---|---|
| GET | `/admin/manual-payments` | `Admin\ManualPaymentController@index` | `admin.manual-payments` |
| POST | `/admin/manual-payments/{payment}/verify` | `Admin\ManualPaymentController@verify` | `admin.manual-payments.verify` |
| POST | `/admin/manual-payments/{payment}/reject` | `Admin\ManualPaymentController@reject` | `admin.manual-payments.reject` |
| GET | `/admin/group-session-payments` | `Admin\PaymentVerificationController@index` | `admin.group-session-payments.index` |
| POST | `/admin/group-session-payments/{payment}/verify` | `Admin\PaymentVerificationController@verify` | `admin.group-session-payments.verify` |
| POST | `/admin/group-session-payments/bulk-verify` | `Admin\PaymentVerificationController@bulkVerify` | `admin.group-session-payments.bulk-verify` |

### `routes/api.php` (CSRF-excluded per `bootstrap/app.php`)
| Method | Path | Controller@action | Name |
|---|---|---|---|
| POST | `/api/daraja/callback` | `DarajaCallbackController@handle` | `daraja.callback` |
| GET | `/api/daraja/status` | `DarajaCallbackController@status` | `daraja.status` |
| POST | `/api/imbank/callback` | `IMBankPaymentController@callback` | `imbank.callback` |
| POST | `/api/imbank/result` | `IMBankPaymentController@result` | `imbank.result` |
| POST | `/api/imbank/timeout` | `IMBankPaymentController@timeout` | `imbank.timeout` |
| GET | `/api/imbank/status` | `IMBankPaymentController@queryStatus` | `imbank.status` |

### Paystack routes today
- **None.** `PaystackWebhookController` and `PaystackService` are deprecated stubs and are **not registered in any route file** (confirmed by grep of `web.php`, `api.php`). `PaystackCallbackHelper` is not referenced anywhere.

---

## 10. Consolidated findings

### Paystack keys present?
- **No `PAYSTACK_*` keys in `.env`** (not even declared). `config/paystack.php` reads `PAYSTACK_PUBLIC_KEY`, `PAYSTACK_SECRET_KEY`, `PAYSTACK_PAYMENT_URL` (default `https://api.paystack.co`), `PAYSTACK_CURRENCY` (default `KES`).
- **Public key name for the JS SDK: `PAYSTACK_PUBLIC_KEY`.** It is currently empty and must be set.

### Current callback / webhook routes
- Active: `POST /api/daraja/callback` + `GET /api/daraja/status` (Daraja M-Pesa); `POST /api/imbank/{callback,result,timeout}` + `GET /api/imbank/status`.
- **No Paystack callback or webhook route exists.** `PaystackWebhookController` (deprecated stub) is unwired.

### Exact files & line ranges needing manual payment UI commented out (customer-facing)
1. **1-on-1:** `resources/views/pages/tutors/show.blade.php`
   - M-Pesa Paybill instructions box: **lines 1359–1382**
   - M-Pesa Transaction Code input: **lines 1384–1392**
   - `processPayment` button (keep the button but rewire the handler): **lines 1393–1406**
   - `processPayment(data)` JS body that sends `mpesa_code` and handles `mode==='manual'`: **lines 1601–1636** (replace the paid-branch to invoke Paystack; keep the free-branch).
2. **Clinic:** `resources/views/pages/clinic-checkout.blade.php`
   - "Pay via M-Pesa Paybill" heading + paybill instructions box + `mpesa_code` form (`#paymentForm` posting to `clinic.pay`): **lines 96–164** (comment out; replace with a Paystack button).
3. **Group session:** `resources/views/pages/group-sessions/checkout.blade.php`
   - "M-Pesa Payment" block incl. `#enrollForm` (phone + `mpesa_code` + "How to pay" steps + submit): **lines 59–143** (comment out; replace with a Paystack button).
4. **Status/confirmation pages (update messaging, not strictly "comment out"):**
   - `resources/views/pages/bookings/payment-pending.blade.php` — displays `$payment->mpesa_code` (lines 124–127) and manual-verification copy; polls `/bookings/payment-status` (JS ~lines 186–220).
   - `resources/views/pages/group-sessions/payment-pending.blade.php` — "M-Pesa code submitted" + "Admin verifies 1-2 hours" copy; no polling.
5. **Back-office (note only — not customer-facing, leave in place):** `resources/views/pages/admin/manual-payments.blade.php`, `resources/views/admin/payments/pending-group-sessions.blade.php`, and `Admin\ManualPaymentController` / `Admin\PaymentVerificationController`.

### Existing Paystack service / payment services
- `app/Services/PaystackService.php` — **deprecated stub**; must be implemented (`initializeTransaction`, `verifyTransaction`, `verifyWebhookSignature`, `generateReference`).
- `app/Http/Controllers/PaystackCallbackHelper.php` — **real, orphaned, reusable**; verifies-nothing itself, just confirms + redirects per `payment_type`. Wire it into the new Paystack callback.
- `app/Http/Controllers/PaystackWebhookController.php` — **deprecated stub**, unwired; re-enable if a webhook is desired.
- `app/Services/DarajaService.php` + `app/Http/Controllers/DarajaCallbackController.php` — the working automated-payment template to mirror (callback → verify → `confirmRelatedRecord` → status poll).

### Payment model fields recap
- **`status` values:** `pending, pending_verification, success, failed, refunded`.
- **`payment_type` values:** `booking` (1-on-1), `clinic`, `group_session`, `course`, `null` (legacy).
- **Reference storage:** `paystack_reference` was dropped; the generic reference column is **`mpesa_reference`** (nullable unique). **Recommend storing the Paystack reference in `mpesa_reference`** and Paystack access_code/channel/gateway_response in `metadata` (JSON). (A dedicated `paystack_reference` column is optional.)
- Commission auto-computed on create: clinic/group_session 25/75; booking 60/40.

---

## 11. Recommended Paystack integration pattern

**Recommendation: Paystack Standard (redirect) flow**, implemented via a new `PaystackPaymentController` that reuses `PaystackCallbackHelper::confirmAndRedirect`, mirroring the existing Daraja callback architecture. The Paystack **Popup (inline) JS SDK** is a viable alternative with better UX but more bespoke code; see the note at the end.

### Why Standard/Redirect over Popup
1. **`PaystackCallbackHelper` is purpose-built for exactly this flow** — a server-side handler that, after a verified payment, confirms the booking/enrollment and redirects to the right success page per `payment_type`. Reusing it (its `confirmAndRedirect` already branches booking vs clinic/group_session) is the smallest, lowest-risk change.
2. It **mirrors the existing Daraja pattern** (`DarajaCallbackController` callback + `/api/daraja/status` poll) already understood in this codebase.
3. Server-side verification is **authoritative** (the return URL handler calls Paystack's verify API), so confirmation does not depend on a browser `onSuccess` callback that can be closed/lost. A webhook can be added later for double-entry hardening.
4. No secret key ever reaches the browser (only `PAYSTACK_PUBLIC_KEY` would, and only if the Popup alternative is chosen).

### Shared backend work (all three flows)
- **Implement `app/Services/PaystackService.php`** (replace stub):
  - `initializeTransaction(array $data): array` — POST `{payment_url}/transaction/initialize` with `Authorization: Bearer {secret_key}`, body `{email, amount (kobo/cents), currency, reference, callback_url, metadata}`. Return `{authorization_url, access_code, reference}`.
  - `verifyTransaction(string $reference): array` — GET `{payment_url}/transaction/verify/{reference}` with bearer secret; return the `data` block (`status`, `gateway_response`, `channel`, `fees`, customer, etc.).
  - `verifyWebhookSignature(string $signature, string $rawBody): bool` — HMAC-SHA512 of raw body vs `secret_key`.
  - `generateReference($prefix)` already exists.
- **Add `.env` keys:** `PAYSTACK_PUBLIC_KEY`, `PAYSTACK_SECRET_KEY`, `PAYSTACK_PAYMENT_URL=https://api.paystack.co`, `PAYSTACK_CURRENCY=KES`.
- **New `app/Http/Controllers/PaystackPaymentController.php`** with:
  - `init(Request)` — creates/loads the `Payment` (status `pending`, `payment_type`, ids, `mpesa_reference` = generated ref, `metadata` with flow + access_code), calls `PaystackService::initializeTransaction`, redirects to `authorization_url`. Branch on a `flow`/`payment_type` param or use per-flow init routes.
  - `callback(Request)` — receives `reference` (and `trxref`) from Paystack's return URL; `PaystackService::verifyTransaction($reference)`; on `status==='success'` and not already success → update `Payment` (`status='success'`, `paid_at=now()`, `mpesa_receipt`/`metadata` with receipt+channel); then `return PaystackCallbackHelper::confirmAndRedirect($payment)`. On failure → `status='failed'`, `failure_reason`, redirect with error.
  - `webhook(Request)` (optional) — verify signature, same update+confirm logic as `callback`, return 200.
- **New routes** (add to `routes/web.php` auth group for init+callback, and `routes/api.php` for the webhook — CSRF-excluded):
  - `POST /paystack/init` (or per-flow) → `PaystackPaymentController@init` — name `paystack.init`
  - `GET /paystack/callback` → `PaystackPaymentController@callback` — name `paystack.callback`
  - `POST /api/paystack/webhook` → `PaystackPaymentController@webhook` (or re-enable `PaystackWebhookController`) — name `paystack.webhook` (CSRF-excluded; Paystack → Paystack secret in `bootstrap/app.php`)

### Per-flow frontend invocation + where the backend lands

**1-on-1 (`show.blade.php`):**
- Comment out paybill UI (1359–1392). In `processPayment` (1601–1636): for paid sessions, POST the booking payload to a new init endpoint (or keep `POST /bookings` to create the booking + pending `Payment`, returning a Paystack `authorization_url`), then `window.location.href = authorization_url`. Free path unchanged.
- Backend: `BookingController::store` (or the init handler) creates `Payment(status='pending', payment_type='booking', mpesa_reference=ref)`, returns `authorization_url`. Paystack returns to `/paystack/callback` → verify → `PaystackCallbackHelper::confirmAndRedirect` → `BookingService::confirmBooking` → redirect `bookings.show`.

**Clinic (`clinic-checkout.blade.php`):**
- Comment out paybill UI + `mpesa_code` form (96–164); replace with a "Pay with Paystack" button → `POST /clinic/{clinic}/paystack/init` (new) or repurpose `clinic.pay` to init Paystack.
- Backend: `ClinicBookingController` init creates `Payment(status='pending', payment_type='clinic', clinic_id, mpesa_reference=ref)`, redirects to Paystack. `/paystack/callback` → verify → helper creates `ClinicEnrollment` (via `confirmClinicEnrollment`) → redirect `clinic.my-bookings`. (No enrollment pre-created at init — consistent with the existing clinic flow and the helper.)

**Group session (`group-sessions/checkout.blade.php`):**
- Comment out the M-Pesa block (59–143); replace with a "Pay with Paystack" button → `POST /group-sessions/{groupSession}/paystack/init` (new) or repurpose `enroll` to init Paystack.
- Backend: init creates `Payment(status='pending', payment_type='group_session', clinic_id, mpesa_reference=ref)`. **Do NOT pre-create the `ClinicEnrollment`** (drop the current provisional enrollment in `enroll()`); let `/paystack/callback` → verify → `PaystackCallbackHelper::confirmClinicEnrollment` create the enrollment with `session_code` → redirect `group-sessions.show`. (This avoids the `isEnrolledBy` "already enrolled" short-circuit that would skip session-code generation.)

### Alternative: Paystack Popup (inline JS SDK)
- Add `<script src="https://js.paystack.co/v1/inline.js"></script>` to `partials/head.blade.php` (currently only Alpine + design CSS are loaded; no Paystack JS anywhere — confirmed by grep).
- Each checkout page calls `PaystackPop.setup({ key: PAYSTACK_PUBLIC_KEY, email, amount, ref, currency:'KES', callback: (res) => POST res.reference to /paystack/verify })`.
- New `GET/POST /paystack/verify` returns JSON `{redirect_url}` (a JSON variant of `PaystackCallbackHelper` logic) instead of a `RedirectResponse`.
- **Trade-off:** better on-page UX (modal, no full redirect), but requires a JSON-returning verify endpoint (the existing helper returns redirects), exposes the public key in the page, and relies on the browser `callback` firing — so server-side verification on `/paystack/verify` is still mandatory, and a webhook is more important for reliability. More new code than the redirect flow.

**Decision:** implement the **Standard/Redirect flow** as the primary path (reuses `PaystackCallbackHelper` directly, mirrors Daraja, authoritative server-side verify). The Popup SDK can be adopted later if a smoother in-page UX is desired, by adding a JSON verify endpoint alongside the redirect callback.

---

### Latent bug to flag (not part of Paystack, but in scope-adjacent code)
- `routes/web.php` registers `GET /clinic/callback → ClinicBookingController@callback` (name `clinic.callback`), but `ClinicBookingController` has **no `callback()` method** — calling it would throw a 500. Either remove the route or implement the method. Worth fixing while touching clinic payment routes.

---

*End of findings. Implementation should proceed from the per-flow line ranges and the recommended pattern above. No source files were modified during this investigation.*
