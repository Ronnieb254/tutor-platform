# Jitsi Bug Fixes (A1–A11) + IntaSend Payment Integration (B1–B8)

This change addresses 11 Jitsi-related bugs and adds IntaSend as a second payment gateway alongside Daraja, enabling Airtel Money and card payments. The Jitsi fixes are largely complete and correct; the IntaSend integration is structurally sound but has two blocking issues — a production-mode webhook signature bypass and hardcoded `meet.jit.si` strings that A11 was supposed to eliminate.

**Watch for:**
- **confirmed** — `verifyWebhook()` allows all traffic whenever `INTASEND_SECRET_KEY` is blank, regardless of `INTASEND_ENVIRONMENT`. Set `INTASEND_ENVIRONMENT=production` without the key and every webhook is accepted.
- **confirmed** — Four `https://meet.jit.si/Mwalimu-session-` strings remain hardcoded in `BookingController`'s `confirm()` and `forceConfirm()` fallback paths. A11 is incomplete.
- **confirmed** — `verifyWebhook()` short-circuits to `true` whenever `$this->environment === 'sandbox'`, bypassing HMAC even when a signature header is present. This is a known sandbox concession per the plan, but its coupling means it stays live until `INTASEND_ENVIRONMENT` is manually changed.

**Verdict**: NEEDS_CHANGES

---

## High-level view

Jitsi service correctness is solid. `JitsiService` has RS256/JaaS-correct `generateJWT()`, public `generateRoomName()` and `generateToken()` wrappers, and the `config/services.php` `jitsi` block is present. `Booking::canJoin()` uses `subMinutes(15)`. The `meeting-room.blade.php` uses `config('services.jitsi.use_jwt')` and the `participantLeft` fetch-POST and `hangup` listener are both gone. `clinic-jitsi-room.blade.php` uses `meet.jit.si`, has the CSRF meta tag, posts attendance, and the Leave button calls `leaveSession()`. All eleven Jitsi tasks pass inspection except A11.

A11 required replacing every hardcoded `https://meet.jit.si/` in `BookingController`'s fallback paths with `config('services.jitsi.domain')`. Four occurrences remain at lines 243, 258, 465, and 514. These are only hit during `confirmBooking()` failure recovery, but they violate the intent of the change and will silently break domain-switch deployments.

The IntaSend service correctly uses `api_ref` as the correlation key in both `createCheckout()` and `parseWebhook()`, ensuring the reference round-trips through the webhook. `IntaSendWebhookController` finds payments by `mpesa_reference`, updates status, and calls `bookingService->confirmBooking()` — the same pattern as `DarajaCallbackController`. `BookingController::store()` and `ClinicBookingController::pay()` both branch correctly on `payment_method`, and both are properly excluded from CSRF.

The signature verification logic in `verifyWebhook()` has two independent bypass paths. The first — `empty($this->secretKey)` — will allow all webhooks on any deployment where the key has not been populated in `.env`, including production. The second — `$this->environment === 'sandbox'` — allows all webhooks unconditionally in sandbox, which is per-spec but is a latent risk if the env variable is forgotten at launch.

The Pay button in `tutors/show.blade.php` still reads `'Pay KSh ' + price` regardless of payment method. The button label update specified in B5 (showing "Pay by Card" or "Pay via Airtel" based on `paymentMethod`) was not applied to the `x-text` expression. This is cosmetic but represents an incomplete B5 implementation.

---

<details>
<summary>Issues (4)</summary>

1. **Hardcoded `meet.jit.si` in BookingController fallbacks (A11 incomplete)** — Lines 243, 258, 465, 514 still contain `'https://meet.jit.si/Mwalimu-session-'`. Replace with `'https://' . config('services.jitsi.domain', 'meet.jit.si') . '/Mwalimu-session-'` in all four locations.

2. **Webhook signature bypass when secret key is missing** — `verifyWebhook()` returns `true` when `$this->secretKey` is empty, regardless of environment. An attacker who discovers the endpoint can forge any payment confirmation. Add a guard: if `$this->environment !== 'sandbox'` and the key is empty, return `false` (or throw) rather than allowing the request.

3. **Pay button label unchanged (B5 incomplete)** — The `x-text` on the pay button in `tutors/show.blade.php` still renders `'Pay KSh ' + price` for all methods. It should switch to `'Pay by Card KSh ' + price` or `'Pay via Airtel KSh ' + price` based on `paymentMethod`, as specified in the B5 plan.

4. **Sandbox bypass persists on `$this->environment === 'sandbox'` regardless of signature presence** — When `INTASEND_ENVIRONMENT=sandbox` and a valid signature header is present, `verifyWebhook()` still returns `true` without checking the HMAC. This is per-spec (sandbox doesn't send real signatures), but the check `return empty($this->secretKey) || $this->environment === 'sandbox'` fires even when a signature header is set, meaning it provides no protection against replay in staging. Consider only skipping when `!$signature` and `$this->environment === 'sandbox'`.

</details>

---

<details>
<summary>Details</summary>

### A11 incomplete — four hardcoded `meet.jit.si` fallback URLs

`BookingController::confirm()` and `forceConfirm()` each have two fallback blocks that run when `confirmBooking()` throws. All four blocks write `'https://meet.jit.si/Mwalimu-session-'` directly into `meeting_url`. The rest of the codebase now reads from `config('services.jitsi.domain')`, so these paths will silently write the wrong domain when deployed against a different Jitsi instance. Confirmed at lines 243, 258, 465, 514 via `Select-String`.

### IntaSend webhook signature bypass

`verifyWebhook()` in `IntaSendService`:

```php
if (!$signature || !$this->secretKey) {
    return empty($this->secretKey) || $this->environment === 'sandbox';
}
$expected = hash_hmac('sha256', $request->getContent(), $this->secretKey);
return hash_equals($expected, $signature);
```

The outer condition fires when `$this->secretKey` is empty. The inner `return` then evaluates `empty($this->secretKey)` → `true`, so every webhook is accepted. Any deployment that omits `INTASEND_SECRET_KEY` from `.env` (including a misconfigured production server) will accept forged webhook calls, allowing an attacker to mark any pending payment as successful without paying.

The second bypass — `$this->environment === 'sandbox'` — is intentional per the plan but also fires when a signature header is present. The safer pattern is:

```php
if ($this->environment === 'sandbox' && !$signature) {
    return true; // sandbox rarely sends signatures
}
if (!$this->secretKey) {
    return false; // missing key is a config error, not a pass
}
$expected = hash_hmac('sha256', $request->getContent(), $this->secretKey);
return hash_equals($expected, $signature);
```

### Clinic IntaSend path — `clinic_id` on Payment without `booking_id`

`ClinicBookingController::pay()` creates `Payment::create([..., 'clinic_id' => $clinic->id, ...])` without a `booking_id`. `IntaSendWebhookController::confirmRelatedRecord()` calls `$payment->booking` to look up the booking. For clinic payments, `$payment->booking` will be `null`, so the webhook's `if ($booking && $booking->status === 'pending')` guard silently skips confirmation. This means clinic Airtel/Card payments through IntaSend will mark the `Payment` as `success` but never create the `ClinicEnrollment`. Severity is lower because M-Pesa clinic payments go through `DarajaCallbackController` which presumably does handle enrollment, but IntaSend clinic payments have no confirmation path.

</details>

---

<details>
<summary>File map</summary>

| File | What changed |
|---|---|
| `app/Services/JitsiService.php` | Added public `generateRoomName()` and `generateToken()` wrappers |
| `app/Models/Booking.php` | `canJoin()` window changed from 60 → 15 minutes |
| `config/services.php` | Added `jitsi` config block (`use_jwt`, `domain`) |
| `bootstrap/app.php` | Added `api/intasend/webhook` to CSRF exclusions |
| `resources/views/pages/bookings/meeting-room.blade.php` | `env()` → `config()` for JWT check; `participantLeft` fetch removed; `hangup` listener removed |
| `resources/views/pages/clinic-jitsi-room.blade.php` | Domain corrected to `meet.jit.si`; CSRF meta added; attendance POST added; Leave button replaced with `leaveSession()` |
| `app/Services/IntaSendService.php` | New service: checkout, webhook verify, webhook parse |
| `app/Http/Controllers/IntaSendWebhookController.php` | New controller: verifies, parses, updates Payment, confirms booking |
| `app/Http/Controllers/BookingController.php` | Added IntaSend branch for airtel/card; phone validation guarded to mpesa-only |
| `app/Http/Controllers/ClinicBookingController.php` | Added IntaSend branch for airtel/card clinic payments |
| `resources/views/pages/tutors/show.blade.php` | Payment method selector added; `processPayment()` updated; redirect branch added |
| `resources/views/pages/clinic-checkout.blade.php` | Alpine payment method selector added; hidden `payment_method` input added |
| `routes/api.php` | `POST /api/intasend/webhook` route registered |

Full diff: `git diff main` in the repository root.

</details>
