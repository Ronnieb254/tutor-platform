# Implementation Plan — Jitsi Fixes (A1–A11) + IntaSend Payments (B1–B8)

## Codebase Findings

- **PHP/Laravel 11**, bootstrap via `bootstrap/app.php`.
- `.env` already has `JITSI_USE_JWT=true` and `JITSI_DOMAIN=meet.jit.si` — A1 is a no-op.
- `JitsiService` already has RS256 logic and `makeJWT()`. Missing: public `generateRoomName()` and `generateToken()` called by `JoinSession.php`.
- `Booking::canJoin()` uses `subMinutes(60)` — needs fixing to 15.
- `meeting-room.blade.php` has both the bad `participantLeft` fetch POST and `hangup` listener — both need removing.
- `meeting-room.blade.php` uses `env('JITSI_USE_JWT')` directly — needs `config('services.jitsi.use_jwt')`.
- `clinic-jitsi-room.blade.php` uses wrong `meet.jitsi.org` domain in both script src and JS const.
- `clinic-jitsi-room.blade.php` Leave button POSTs a logout form — needs replacing with JS `leaveSession()`.
- `config/services.php` has no `jitsi` key yet.
- `BookingService::confirmBooking()` hardcodes `meet.jit.si` in the meeting_url fallback inside `BookingController` (not in the service itself — the service uses `JitsiService::getJoinUrl()` which already reads from env).
- `BookingController` has hardcoded `https://meet.jit.si/...` strings in fallback blocks.
- Payment gateway in use: Daraja M-Pesa (STK Push). Task adds IntaSend for Airtel Money and Cards.
- `DarajaCallbackController::confirmRelatedRecord()` is the canonical booking-confirmation hook — IntaSend webhook must replicate this pattern.
- `bootstrap/app.php` CSRF exclusion array currently has only `api/daraja/callback`.
- `clinic-checkout.blade.php` is a standard form POST (not Alpine/fetch) — will be converted to Alpine for method switching.
- `tutors/show.blade.php` Alpine `x-data` already has `phone`, `processPayment()`, and `showError()` defined globally. Need to add `paymentMethod: 'mpesa'` and update `processPayment()`.
- `Payment` model uses `mpesa_reference` field as the canonical polling key — IntaSend will store its reference in the same column.

---

## TASK A — Fix Jitsi Bugs

- [ ] 1. **A1: Verify `.env` already has JITSI_USE_JWT=true** (confirmed present — no edit needed).
      Record this as a no-op so the coder does not waste time re-adding it.
      Files: none
      Verify: `grep JITSI_USE_JWT .env` — output shows `JITSI_USE_JWT=true`.

- [ ] 2. **A2: Verify JitsiService RS256/JaaS correctness** (confirmed present and correct).
      `__construct` already overrides domain to `8x8.vc` for vpaas-magic-cookie appId.
      `generateJWT()` already uses RS256 with the appId as `kid`.
      No changes needed; mark as confirmed correct.
      Files: none
      Verify: `php artisan tinker --execute="app(\App\Services\JitsiService::class)->isConfigured();"` returns `true`.

- [ ] 3. **A3: Add missing public methods to JitsiService**
      Add `generateRoomName(Booking $booking): string` and `generateToken(User $user, string $roomName, bool $isModerator): ?string` as public methods.
      `generateRoomName` returns `$this->sanitizeRoomName($booking->meeting_room ?? 'Mwalimu-session-' . $booking->uuid)`.
      `generateToken` calls `$this->makeJWT($roomName, $user->name, $isModerator, ['email' => $user->email])`.
      Add `use App\Models\Booking;` and `use App\Models\User;` imports at the top of the file.
      Files: `app/Services/JitsiService.php`
      Verify: `php artisan tinker --execute="echo method_exists(app(\App\Services\JitsiService::class), 'generateRoomName') ? 'OK' : 'FAIL';"` → `OK`.

- [ ] 4. **A4: Fix clinic-jitsi-room.blade.php — wrong domain and missing CSRF meta**
      Change `<script src="https://meet.jitsi.org/external_api.js">` to `<script src="https://meet.jit.si/external_api.js">`.
      Change `const domain = "meet.jitsi.org";` to `const domain = "meet.jit.si";`.
      Add `<meta name="csrf-token" content="{{ csrf_token() }}">` inside `<head>` after the existing viewport meta.
      Files: `resources/views/pages/clinic-jitsi-room.blade.php`
      Verify: `grep -c "meet.jit.si" resources/views/pages/clinic-jitsi-room.blade.php` → at least 2 (script src + JS const). Then: `php artisan view:clear && php artisan config:clear`.

- [ ] 5. **A5: Remove spurious fetch POST from participantLeft handler**
      In `meeting-room.blade.php`, in the `participantLeft` event listener block, remove the `fetch(leftUrl, ...)` call entirely, keeping only `pollAttendance();`.
      The block after the change should be: `api.addEventListener('participantLeft', () => { pollAttendance(); });`
      Files: `resources/views/pages/bookings/meeting-room.blade.php`
      Verify: `grep -c "participantLeft" resources/views/pages/bookings/meeting-room.blade.php` → 1 line (the addEventListener line only, no fetch inside it).

- [ ] 6. **A6: Fix Booking::canJoin() early-open window from 60 to 15 minutes**
      In `app/Models/Booking.php`, change:
      `$opensAt = $this->scheduled_at->copy()->subMinutes(60);`
      to:
      `$opensAt = $this->scheduled_at->copy()->subMinutes(15);`
      Files: `app/Models/Booking.php`
      Verify: `php artisan tinker --execute="echo (new \App\Models\Booking)->canJoin() === false ? 'OK' : 'SKIP';"` runs without error.

- [ ] 7. **A7: Remove hangup event listener from meeting-room.blade.php**
      Remove the entire block:
      ```js
      api.addEventListener('hangup', () => {
          window.location.href = bookingUrl;
      });
      ```
      Files: `resources/views/pages/bookings/meeting-room.blade.php`
      Verify: `grep "hangup" resources/views/pages/bookings/meeting-room.blade.php` → no output (the line is gone).

- [ ] 8. **A8: Add jitsi key to config/services.php + use config() in meeting-room.blade.php**
      In `config/services.php`, append to the returned array:
      ```php
      'jitsi' => [
          'use_jwt' => env('JITSI_USE_JWT', false),
          'domain'  => env('JITSI_DOMAIN', 'meet.jit.si'),
      ],
      ```
      In `meeting-room.blade.php`, find:
      `@if(app(\App\Services\JitsiService::class)->isConfigured() && env('JITSI_USE_JWT'))`
      Replace with:
      `@if(app(\App\Services\JitsiService::class)->isConfigured() && config('services.jitsi.use_jwt'))`
      Files: `config/services.php`, `resources/views/pages/bookings/meeting-room.blade.php`
      Verify: `php artisan tinker --execute="dump(config('services.jitsi'));"` → shows `use_jwt` and `domain` keys with values from `.env`.

- [ ] 9. **A9: Add attendance POST to clinic-jitsi-room.blade.php videoConferenceJoined handler**
      Replace the stub `videoConferenceJoined` handler (which only has a `console.log`) with:
      ```js
      jitsiAPI.addEventListener('videoConferenceJoined', function() {
          fetch('/clinic/' + {{ $clinic->id }} + '/attendance', {
              method: 'POST',
              headers: {
                  'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                  'Content-Type': 'application/json',
              },
              body: JSON.stringify({ user_id: {{ auth()->id() }} }),
          }).catch(function() {});
      });
      ```
      Note: the CSRF meta tag added in A4 must be present first (A4 is a prerequisite).
      Files: `resources/views/pages/clinic-jitsi-room.blade.php`
      Verify: File renders without Blade errors: `php artisan view:cache` completes with no exception.

- [ ] 10. **A10: Replace logout Leave button with JS leaveSession() in clinic-jitsi-room.blade.php**
       Remove the `<form method="POST" action="{{ route('logout') }}">` block that contains the "Leave Session" button.
       Replace with:
       ```html
       <button onclick="leaveSession()" class="exit-btn">
           <i class="fa-solid fa-right-from-bracket"></i> Leave Session
       </button>
       ```
       Add the `leaveSession()` function in the `<script>` block (before the closing `</script>` tag):
       ```js
       function leaveSession() {
           try { jitsiAPI.executeCommand('hangup'); } catch(e) {}
           setTimeout(function() {
               window.location.href = '{{ route("clinic.my-bookings") }}';
           }, 1000);
       }
       ```
       If `route('clinic.my-bookings')` does not exist, use `'{{ url("/") }}'` as fallback (verify the route name first with `php artisan route:list --name=clinic`).
       Files: `resources/views/pages/clinic-jitsi-room.blade.php`
       Verify: `php artisan view:cache` completes without errors. Confirm no `route('logout')` call remains in this file.

- [ ] 11. **A11: Use config('services.jitsi.domain') in BookingService and BookingController fallbacks**
       In `app/Services/BookingService.php`, `confirmBooking()` builds `$studentJoinUrl` via `$this->jitsi->getJoinUrl()`, which already reads from env. No change needed in the service itself.
       In `app/Http/Controllers/BookingController.php`, there are several hardcoded `'https://meet.jit.si/...'` strings in fallback blocks inside `confirm()`, `forceConfirm()`, and the fallback inside `forceConfirm()`. Replace all hardcoded `https://meet.jit.si/` with `'https://' . config('services.jitsi.domain', 'meet.jit.si') . '/'` — i.e., replace the domain prefix dynamically.
       Specifically:
       - In `confirm()` method: two occurrences of `'https://meet.jit.si/Mwalimu-session-'`
       - In `forceConfirm()` method: two more occurrences of `'https://meet.jit.si/Mwalimu-session-'`
       Replace each with: `'https://' . config('services.jitsi.domain', 'meet.jit.si') . '/Mwalimu-session-'`
       Files: `app/Http/Controllers/BookingController.php`
       Verify: `php artisan config:cache` succeeds. `grep -r "meet.jit.si" app/Http/Controllers/BookingController.php` → no hardcoded strings remain.

---

## TASK B — Add Airtel Money and Card via IntaSend

- [ ] 12. **B1: Add IntaSend credentials to .env**
       Append the following to `.env` (after the existing IMBANK block):
       ```
       INTASEND_PUBLISHABLE_KEY=
       INTASEND_SECRET_KEY=
       INTASEND_ENVIRONMENT=sandbox
       ```
       Files: `.env`
       Verify: `grep INTASEND .env` → shows all three keys.

- [ ] 13. **B2: Create IntaSendService**
       Create `app/Services/IntaSendService.php` with the following:
       - Constructor reads `INTASEND_PUBLISHABLE_KEY`, `INTASEND_SECRET_KEY`, `INTASEND_ENVIRONMENT` from env.
       - Determines base URL: sandbox → `https://sandbox.intasend.com/api/v1/checkout/`; production → `https://api.intasend.com/api/v1/checkout/`.
       - Method `createCheckout(float $amount, User $user, string $reference, string $redirectUrl): array`:
         - POST to checkout URL with body: `{public_key, currency:'KES', amount, email, first_name, last_name, redirect_url, webhook_url}`.
         - `webhook_url` = `url('/api/intasend/webhook')`.
         - `first_name`/`last_name` split from `$user->name` (split on first space; fallback both to full name if no space).
         - Returns `['url' => $response['url'], 'id' => $response['id'] ?? $reference]`.
         - Throws `\RuntimeException` with message from response if `url` key is missing.
       - Method `verifyWebhook(Request $request): bool`:
         - IntaSend signs webhooks with HMAC-SHA256 of the raw body using the secret key.
         - Compute `hash_hmac('sha256', $request->getContent(), env('INTASEND_SECRET_KEY'))`.
         - Compare against `$request->header('X-IntaSend-Signature')`.
         - Return `true` if they match; `false` otherwise.
         - In sandbox mode (`INTASEND_ENVIRONMENT=sandbox`), return `true` unconditionally (Safaricom sandbox does not send real signatures).
       - Method `parseWebhook(array $payload): array`:
         - Returns `['status' => $status, 'reference' => $payload['invoice']['account'] ?? $payload['invoice']['id'] ?? '', 'amount' => $payload['invoice']['net_amount'] ?? 0, 'method' => $payload['invoice']['payment_method'] ?? 'card', 'receipt' => $payload['invoice']['id'] ?? '']`.
         - Map IntaSend `COMPLETE` status to `'success'`, anything else to `'failed'`.
         - The `account` field in the IntaSend webhook is what we pass as `account` in the checkout request — use this to store our internal `$reference`.
       - Use Laravel's `Http` facade for the POST request.
       - Add `use App\Models\User;` and `use Illuminate\Http\Request;` imports.
       Files: `app/Services/IntaSendService.php`
       Verify: `php artisan tinker --execute="app(\App\Services\IntaSendService::class);"` — no exceptions (class resolves from container).

- [ ] 14. **B3: Create IntaSendWebhookController**
       Create `app/Http/Controllers/IntaSendWebhookController.php`:
       ```php
       namespace App\Http\Controllers;

       use App\Models\Payment;
       use App\Services\BookingService;
       use App\Services\IntaSendService;
       use Illuminate\Http\Request;
       use Illuminate\Support\Facades\Log;

       class IntaSendWebhookController extends Controller
       {
           public function __construct(
               protected IntaSendService $intaSend,
               protected BookingService  $bookingService
           ) {}

           public function handle(Request $request): \Illuminate\Http\JsonResponse
           {
               if (!$this->intaSend->verifyWebhook($request)) {
                   Log::warning('IntaSend webhook: invalid signature');
                   return response()->json(['status' => 'rejected'], 401);
               }

               $payload = $request->all();
               Log::info('IntaSend webhook received', ['preview' => array_slice($payload, 0, 3)]);

               $parsed = $this->intaSend->parseWebhook($payload);

               $payment = Payment::where('mpesa_reference', $parsed['reference'])->first();

               if (!$payment) {
                   Log::warning('IntaSend webhook: payment not found', ['reference' => $parsed['reference']]);
                   return response()->json(['status' => 'ok']);
               }

               if ($payment->status === 'success') {
                   return response()->json(['status' => 'ok']);
               }

               if ($parsed['status'] === 'success') {
                   $payment->update([
                       'status'   => 'success',
                       'paid_at'  => now(),
                       'mpesa_receipt' => $parsed['receipt'],
                   ]);
                   $this->confirmRelatedRecord($payment);
               } else {
                   $payment->update([
                       'status'         => 'failed',
                       'failure_reason' => 'IntaSend: ' . $parsed['status'],
                   ]);
               }

               return response()->json(['status' => 'ok']);
           }

           private function confirmRelatedRecord(Payment $payment): void
           {
               // Reuse same logic as DarajaCallbackController
               $booking = $payment->booking;
               if ($booking && $booking->status === 'pending') {
                   try {
                       $this->bookingService->confirmBooking($booking);
                   } catch (\Throwable $e) {
                       Log::error('IntaSend webhook: confirmBooking failed', [
                           'booking_id' => $booking->id,
                           'error'      => $e->getMessage(),
                       ]);
                   }
               }
           }
       }
       ```
       Files: `app/Http/Controllers/IntaSendWebhookController.php`
       Verify: `php artisan route:list --name=intasend` — after B6 routes are added, this shows the webhook route.

- [ ] 15. **B4: Update BookingController::store() to support airtel and card via IntaSend**
       Add `payment_method` to validation (at the top of `store()`):
       ```php
       'payment_method' => 'nullable|in:mpesa,airtel,card',
       ```
       After the free-session early return, extract the payment method:
       ```php
       $paymentMethod = $request->input('payment_method', 'mpesa');
       ```
       For `mpesa` (or default): keep the existing Daraja STK Push path unchanged.
       For `airtel` or `card`: skip the phone validation, generate a reference, create a `Payment` record with `status='pending'` and `mpesa_reference=$reference` (no `checkout_request_id`), then call IntaSendService:
       ```php
       if (in_array($paymentMethod, ['airtel', 'card'])) {
           $reference = 'IS-' . strtoupper(\Illuminate\Support\Str::random(16));
           try {
               $intaSend = app(\App\Services\IntaSendService::class);
               $checkout = $intaSend->createCheckout(
                   (float) $booking->total_amount,
                   auth()->user(),
                   $reference,
                   route('bookings.show', $booking->uuid)
               );
               Payment::create([
                   'booking_id'      => $booking->id,
                   'user_id'         => auth()->id(),
                   'amount'          => $booking->total_amount,
                   'currency'        => 'KES',
                   'mpesa_reference' => $reference,
                   'status'          => 'pending',
               ]);
               return response()->json([
                   'status'       => 'ok',
                   'reference'    => $reference,
                   'redirect_url' => $checkout['url'],
               ]);
           } catch (\Exception $e) {
               $booking->delete();
               return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
           }
       }
       ```
       The phone validation `$request->validate(['phone' => 'required|...'])` must be wrapped in an `if ($paymentMethod === 'mpesa')` guard so card/airtel users are not rejected for missing phone.
       Files: `app/Http/Controllers/BookingController.php`
       Verify: `php artisan route:list --name=bookings` shows existing booking routes. `php artisan tinker --execute="app(\App\Http\Controllers\BookingController::class);"` resolves without error.

- [ ] 16. **B5: Update tutors/show.blade.php — add payment method selector**
       In the Alpine `x-data` object (the `tab-book` div), add `paymentMethod: 'mpesa'` to the data properties.
       Before the M-Pesa phone `<div>` (inside the `@auth` block in step 2), add a payment method selector:
       ```html
       {{-- Payment Method Selector --}}
       <div style="margin-bottom:14px;">
           <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);margin-bottom:8px;">Payment Method</p>
           <div style="display:flex;gap:8px;flex-wrap:wrap;">
               <button type="button" @click="paymentMethod='mpesa'"
                   :style="paymentMethod==='mpesa' ? 'border-color:#0A64BC;background:rgba(10,100,188,.08);' : ''"
                   style="flex:1;min-width:100px;padding:10px;border-radius:10px;border:1.5px solid rgba(21,40,31,.18);font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;background:#fff;transition:all .15s;">
                   📱 M-Pesa
               </button>
               <button type="button" @click="paymentMethod='airtel'"
                   :style="paymentMethod==='airtel' ? 'border-color:#0A64BC;background:rgba(10,100,188,.08);' : ''"
                   style="flex:1;min-width:100px;padding:10px;border-radius:10px;border:1.5px solid rgba(21,40,31,.18);font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;background:#fff;transition:all .15s;">
                   📶 Airtel Money
               </button>
               <button type="button" @click="paymentMethod='card'"
                   :style="paymentMethod==='card' ? 'border-color:#0A64BC;background:rgba(10,100,188,.08);' : ''"
                   style="flex:1;min-width:100px;padding:10px;border-radius:10px;border:1.5px solid rgba(21,40,31,.18);font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;background:#fff;transition:all .15s;">
                   💳 Card
               </button>
           </div>
       </div>
       ```
       Wrap the existing phone input `<div>` with `x-show="paymentMethod === 'mpesa' || paymentMethod === 'airtel'"`.
       Add a card notice below the phone div:
       ```html
       <div x-show="paymentMethod === 'card'" style="background:rgba(10,100,188,.05);border:1px solid rgba(10,100,188,.15);border-radius:10px;padding:12px 14px;margin-bottom:14px;font-size:13px;color:#0A64BC;">
           <i class="fa-solid fa-credit-card"></i> You'll be redirected to a secure card payment page.
       </div>
       ```
       Update the Pay button label dynamically:
       Change `<span x-text="isFree ? 'Book Free Session' : 'Pay KSh ' + price "></span>`
       to `<span x-text="isFree ? 'Book Free Session' : (paymentMethod === 'card' ? 'Pay by Card KSh ' + price : paymentMethod === 'airtel' ? 'Pay via Airtel KSh ' + price : 'Pay KSh ' + price)"></span>`.
       Update `processPayment()` in the `<script>` block:
       1. Change the phone validation guard to only run when `paymentMethod !== 'card'`:
          ```js
          if (data.paymentMethod !== 'card' && (!data.phone || data.phone.length < 9)) {
              showError('Invalid Phone Number', 'Please enter your phone number.');
              return;
          }
          ```
       2. Add `payment_method: data.paymentMethod` to the fetch body JSON.
       3. After `.then(res => {`, add a redirect branch for airtel/card before the existing mpesa polling branch:
          ```js
          if (res.status === 'ok' && res.redirect_url) {
              window.location.href = res.redirect_url;
              return;
          }
          ```
       Files: `resources/views/pages/tutors/show.blade.php`
       Verify: `php artisan view:cache` completes without Blade compilation errors.

- [ ] 17. **B6: Add IntaSend webhook route and CSRF exclusion**
       In `routes/api.php`, add after the existing I&M Bank routes:
       ```php
       use App\Http\Controllers\IntaSendWebhookController;

       Route::post('/intasend/webhook', [IntaSendWebhookController::class, 'handle'])
           ->name('intasend.webhook');
       ```
       In `bootstrap/app.php`, update the CSRF exclusion array:
       ```php
       $middleware->validateCsrfTokens(except: [
           'api/daraja/callback',
           'api/intasend/webhook',
       ]);
       ```
       Files: `routes/api.php`, `bootstrap/app.php`
       Verify: `php artisan route:list --name=intasend.webhook` shows one POST route at `api/intasend/webhook`.

- [ ] 18. **B7: Document IntaSend reference field usage**
       This is an informational note baked into code comments, not a code change.
       Add a PHPDoc comment to `IntaSendWebhookController::handle()` and to the `Payment` creation block in `BookingController::store()` (for the airtel/card path):
       ```
       // IntaSend payments store our internal reference in mpesa_reference.
       // This allows the existing /api/daraja/status polling endpoint to work
       // without modification — the frontend polls by reference regardless of method.
       ```
       Files: `app/Http/Controllers/IntaSendWebhookController.php`, `app/Http/Controllers/BookingController.php`
       Verify: No code change — just comments. `php artisan config:clear` passes.

- [ ] 19. **B8: Update clinic-checkout.blade.php to support Airtel/Card payment methods**
       The clinic checkout is a standard form POST to `route('clinic.pay', $clinic)`. The current form has no `payment_method` input and only an M-Pesa section.
       Convert the payment section to Alpine.js:
       1. Add `x-data="{ paymentMethod: 'mpesa' }"` to the outer `<div class="container">`.
       2. Replace the static M-Pesa-only payment method card block with three clickable cards (M-Pesa, Airtel Money, Card) that set `paymentMethod`.
       3. Show the phone input only when `x-show="paymentMethod === 'mpesa' || paymentMethod === 'airtel'"`.
       4. Add a card notice: `x-show="paymentMethod === 'card'"` — "You'll be redirected to a secure card payment page."
       5. Add a hidden input `<input type="hidden" name="payment_method" :value="paymentMethod">` inside the `<form>`.
       6. Update the submit button label dynamically:
          `x-text="paymentMethod === 'card' ? 'Pay by Card KSh ...' : paymentMethod === 'airtel' ? 'Pay via Airtel KSh ...' : 'Pay KSh ...'"`.
       7. The form action remains `route('clinic.pay', $clinic)` — this controller action must also be updated to handle the `payment_method` field using the same IntaSend pattern as BookingController (create Payment, call IntaSendService, redirect to IntaSend URL for card/airtel, or fall through to existing STK Push for mpesa). Locate the controller handling `route('clinic.pay')` and apply equivalent changes.
          - Find the route: `php artisan route:list --name=clinic.pay` to identify the controller/method.
          - Apply the same branching logic as B4: if `payment_method` is `airtel` or `card`, call `IntaSendService::createCheckout()` and redirect; otherwise, proceed with Daraja.
       Files: `resources/views/pages/clinic-checkout.blade.php`, and the controller handling `clinic.pay` route (to be identified via `php artisan route:list --name=clinic.pay`)
       Verify: `php artisan view:cache` compiles without errors. `php artisan route:list --name=clinic.pay` shows the route exists.

---

## Verification (run all after all items are complete)

```bash
# 1. Clear all caches
php artisan config:clear && php artisan view:clear && php artisan route:clear && php artisan cache:clear

# 2. Re-cache for production
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 3. Confirm Jitsi service resolves and has the new methods
php artisan tinker --execute="
\$j = app(\App\Services\JitsiService::class);
echo method_exists(\$j, 'generateRoomName') ? 'generateRoomName: OK' : 'generateRoomName: FAIL';
echo PHP_EOL;
echo method_exists(\$j, 'generateToken') ? 'generateToken: OK' : 'generateToken: FAIL';
echo PHP_EOL;
echo config('services.jitsi.use_jwt') ? 'JWT config: OK' : 'JWT config: FAIL';
"

# 4. Confirm IntaSend service resolves
php artisan tinker --execute="app(\App\Services\IntaSendService::class); echo 'IntaSend: OK';"

# 5. Confirm routes exist
php artisan route:list --name=intasend.webhook
php artisan route:list --name=clinic.pay

# 6. Run any existing tests (if test suite exists)
php artisan test --stop-on-failure
```

---

## Notes & Assumptions

- **A1**: `.env` already has `JITSI_USE_JWT=true` and `JITSI_DOMAIN=meet.jit.si`. No edit needed.
- **A2**: RS256 logic is already correct in `JitsiService::generateJWT()`. No edit needed.
- **A3**: `JoinSession.php` calls `$jitsi->generateRoomName($this->booking)` and `$jitsi->generateToken($user, $this->roomName, $isTutor)` — these must be public. `makeJWT()` already exists and is public; `generateToken()` is a thin public wrapper calling `makeJWT()`.
- **A9**: Route `/clinic/{clinic}/attendance` is assumed to exist. If not, the fetch will silently fail (fire-and-forget with `.catch(() => {})`), which is acceptable per the spec.
- **A10**: Uses `route('clinic.my-bookings')` — verify with `php artisan route:list --name=clinic.my-bookings`; fall back to `url('/')` if missing.
- **B7**: `mpesa_reference` is the canonical polling key. The existing `/api/daraja/status` endpoint already looks up by `mpesa_reference`, so card/airtel payments stored with that field are automatically polled by the frontend without changes.
- **B8**: The clinic checkout controller action needs to be identified before implementing. The coder must run `php artisan route:list --name=clinic.pay` first.
- **IntaSend sandbox**: In sandbox mode, webhook signature verification is skipped (sandbox doesn't send valid HMAC). The `verifyWebhook()` method returns `true` when `INTASEND_ENVIRONMENT=sandbox`.
