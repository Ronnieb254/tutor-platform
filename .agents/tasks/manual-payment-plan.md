# Manual Payment Implementation Plan

**Paybill:** 542542 · **Account Number:** 313126 · **Account Name:** OBARE OKOTH JACOB  
**Verdict file watched:** `.agents/tasks/manual-payment-review.json`

---

## 1 — IntaSend cleanup

**Files to delete:**
- `app/Services/IntaSendService.php`
- `app/Http/Controllers/IntaSendWebhookController.php`

**Lines to remove (do NOT delete the files — just gut the references):**

| File | What to remove |
|------|---------------|
| `app/Http/Controllers/BookingController.php` | `use App\Services\IntaSendService;` import; `protected IntaSendService $intaSend` constructor param; the entire `if (in_array($paymentMethod, ['airtel', 'card']))` block that calls `$this->intaSend->createCheckout(...)` |
| `app/Http/Controllers/ClinicBookingController.php` | `use App\Services\IntaSendService;` import; `protected $intaSend;` property; `IntaSendService $intaSend` constructor param; `$this->intaSend = $intaSend;` assignment; the entire `if (in_array($paymentMethod, ['airtel', 'card']))` block |
| `routes/api.php` | The `use App\Http\Controllers\IntaSendWebhookController;` line and the `Route::post('/intasend/webhook', ...)` route |
| `.env` | `INTASEND_PUBLISHABLE_KEY`, `INTASEND_SECRET_KEY`, `INTASEND_ENVIRONMENT` — comment them out with `#` |
| `bootstrap/app.php` | In `validateCsrfTokens(except:...)` remove `'api/intasend/webhook'` |

**Daraja STK Push — comment out (do NOT delete DarajaService):**

In `BookingController::store()`, wrap the entire M-Pesa STK Push block (`// --- M-Pesa STK Push (default) ---` down to the last `catch` block) in `/* ... */`.  
In `ClinicBookingController::pay()`, wrap the `// --- M-Pesa STK Push (default) ---` block similarly.

---

## 2 — Migration: add manual-payment columns to payments table

Create `database/migrations/2026_11_12_000001_add_manual_payment_fields_to_payments_table.php`:

```php
Schema::table('payments', function (Blueprint $table) {
    $table->string('mpesa_code')->nullable()->after('mpesa_receipt');          // student-entered M-Pesa code
    $table->text('admin_note')->nullable()->after('mpesa_code');               // rejection reason or note
    $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('admin_note');
    $table->timestamp('verified_at')->nullable()->after('verified_by');
});
```

Also update the `status` enum in the payments table. Because MySQL enum altering can be fragile, add a new migration that runs:
```php
DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','pending_verification','success','failed','refunded') NOT NULL DEFAULT 'pending'");
```

Update `Payment::$fillable` to include `['mpesa_code','admin_note','verified_by','verified_at']`.

**Verify:** `php artisan migrate` completes without error.

---

## 3 — BookingController::store() rewrite

Replace the entire payment-method section (everything after the free-session block) with the following logic:

```php
// All paid bookings now use manual Paybill payment
$request->validate(['mpesa_code' => 'required|string|min:6|max:30']);

$payment = Payment::create([
    'booking_id'   => $booking->id,
    'user_id'      => auth()->id(),
    'amount'       => $booking->total_amount,
    'currency'     => 'KES',
    'payment_type' => 'booking',
    'status'       => 'pending_verification',
    'mpesa_code'   => strtoupper(trim($request->input('mpesa_code'))),
    'metadata'     => [
        'payment_instructions' => 'Paybill 542542, Account 313126',
        'student_submitted_at' => now()->toDateTimeString(),
    ],
]);

return response()->json([
    'status'            => 'pending_verification',
    'booking_id'        => $booking->id,
    'booking_uuid'      => $booking->uuid,
    'payment_id'        => $payment->id,
    'message'           => 'Your booking is pending. Admin will verify your payment within 5 minutes and send your session codes by email.',
]);
```

Remove the `protected DarajaService $daraja` and `protected IntaSendService $intaSend` constructor injections (keep `BookingService`).

---

## 4 — New paymentStatus() method in BookingController

Add to `BookingController`:

```php
public function paymentStatus(Request $request)
{
    $request->validate(['payment_id' => 'required|integer']);

    $payment = Payment::where('id', $request->payment_id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $booking = $payment->booking;

    return response()->json([
        'status'        => $payment->status,          // pending_verification | success | failed
        'booking_uuid'  => $booking?->uuid,
        'student_code'  => $booking?->session_code_student,
        'booking_url'   => $booking ? route('bookings.show', $booking->uuid) : null,
        'admin_note'    => $payment->status === 'failed' ? $payment->admin_note : null,
        'verified_at'   => $payment->verified_at?->toDateTimeString(),
    ]);
}
```

Route: `GET /bookings/payment-status` → `bookings.payment-status` (authenticated, inside the main `auth` group in web.php).

---

## 5 — ClinicBookingController::pay() rewrite

Remove both constructor injections entirely (`DarajaService`, `IntaSendService`). The constructor becomes `public function __construct() {}` or is dropped.

Replace the payment method dispatch block with:

```php
$request->validate(['mpesa_code' => 'required|string|min:6|max:30']);

$payment = Payment::create([
    'user_id'      => $user->id,
    'clinic_id'    => $clinic->id,
    'amount'       => $clinic->price,
    'currency'     => 'KES',
    'payment_type' => 'clinic',
    'status'       => 'pending_verification',
    'mpesa_code'   => strtoupper(trim($request->input('mpesa_code'))),
    'metadata'     => [
        'clinic_id'    => $clinic->id,
        'clinic_title' => $clinic->title ?? $clinic->name,
        'payment_instructions' => 'Paybill 542542, Account 313126',
    ],
]);

return redirect()->route('clinic.my-bookings')
    ->with('info', 'Your payment code has been submitted. Admin will verify within 5 minutes and send your session code by email.');
```

---

## 6 — ManualPaymentController (Admin)

Create `app/Http/Controllers/Admin/ManualPaymentController.php`:

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ClinicEnrollment;
use App\Notifications\BookingConfirmedStudent;
use App\Notifications\BookingConfirmedTutor;
use App\Notifications\PaymentRejected;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManualPaymentController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    /** index(): paginated list of pending_verification payments, newest first */
    public function index()
    {
        $payments = Payment::where('status', 'pending_verification')
            ->with(['user', 'booking.tutor', 'booking.student', 'booking.subject'])
            ->latest()
            ->paginate(20);

        $recentlyVerified = Payment::whereIn('status', ['success', 'failed'])
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', now()->subHours(24))
            ->with(['user', 'booking', 'verifiedBy'])
            ->latest('verified_at')
            ->limit(10)
            ->get();

        return view('pages.admin.manual-payments', compact('payments', 'recentlyVerified'));
    }

    /** verify(): mark payment success, confirm related booking/clinic, send codes */
    public function verify(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending_verification') {
            return back()->with('error', 'Payment is not pending verification.');
        }

        DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'status'      => 'success',
                'paid_at'     => now(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'admin_note'  => $request->input('note'),
            ]);

            // Booking payment
            if ($payment->booking_id) {
                $this->confirmRelatedBooking($payment);
            }

            // Clinic payment
            if ($payment->clinic_id) {
                $this->confirmRelatedClinic($payment);
            }
        });

        return back()->with('success', 'Payment verified. Session codes sent to student and tutor.');
    }

    /** reject(): mark payment failed, notify student */
    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        if ($payment->status !== 'pending_verification') {
            return back()->with('error', 'Payment is not pending verification.');
        }

        $payment->update([
            'status'      => 'failed',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'admin_note'  => $request->input('reason'),
        ]);

        // Cancel associated booking so it doesn't sit as pending
        if ($payment->booking) {
            $payment->booking->update([
                'status'              => 'cancelled',
                'cancellation_reason' => 'Payment rejected: ' . $request->input('reason'),
                'cancelled_at'        => now(),
            ]);
        }

        try {
            Notification::sendNow($payment->user, new PaymentRejected($payment));
        } catch (\Throwable $e) {
            \Log::warning('PaymentRejected notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Payment rejected. Student has been notified.');
    }

    /** confirmRelatedBooking(): generate session codes and notify both parties */
    private function confirmRelatedBooking(Payment $payment): void
    {
        $booking = $payment->booking->fresh();

        if ($booking->status === 'confirmed') {
            return; // Already confirmed
        }

        try {
            $this->bookingService->confirmBooking($booking);
        } catch (\Throwable $e) {
            // Fallback: direct update
            $booking->update([
                'status'               => 'confirmed',
                'session_code_tutor'   => 'TUT-' . strtoupper(Str::random(6)),
                'session_code_student' => 'STU-' . strtoupper(Str::random(6)),
                'meeting_room'         => 'Mwalimu-session-' . $booking->uuid,
                'meeting_url'          => 'https://meet.jit.si/Mwalimu-session-' . $booking->uuid,
            ]);
            $booking->refresh()->load(['student', 'tutor', 'subject']);
            try {
                Notification::sendNow($booking->student, new BookingConfirmedStudent($booking));
            } catch (\Throwable $ne) {}
            try {
                Notification::sendNow($booking->tutor, new BookingConfirmedTutor($booking));
            } catch (\Throwable $ne) {}
        }
    }

    /** confirmRelatedClinic(): create ClinicEnrollment with session code */
    private function confirmRelatedClinic(Payment $payment): void
    {
        $already = ClinicEnrollment::where('payment_id', $payment->id)->exists();
        if ($already) return;

        $sessionCode = 'CLI-' . strtoupper(Str::random(6));
        ClinicEnrollment::create([
            'clinic_id'    => $payment->clinic_id,
            'student_id'   => $payment->user_id,
            'payment_id'   => $payment->id,
            'session_code' => $sessionCode,
            'attended'     => false,
        ]);

        // Notify student
        try {
            $payment->user->notify(new \App\Notifications\ClinicEnrollmentConfirmed(
                $payment->clinic,
                $sessionCode
            ));
        } catch (\Throwable $e) {}
    }
}
```

Add `verified_by` relationship to `Payment` model:
```php
public function verifiedBy() { return $this->belongsTo(User::class, 'verified_by'); }
```

---

## 7 — PaymentRejected notification

Create `app/Notifications/PaymentRejected.php` following the exact pattern of `BookingConfirmedStudent`:

```php
namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejected extends Notification
{
    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array { return ['database', 'mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Not Verified – Action Required')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Your payment submission (code: ' . $this->payment->mpesa_code . ') could not be verified.')
            ->line('Reason: ' . ($this->payment->admin_note ?? 'No reason provided.'))
            ->line('Amount: KSh ' . number_format($this->payment->amount, 2))
            ->line('Please re-submit with the correct M-Pesa code, or contact support.')
            ->action('Book Again', url('/tutors'))
            ->line('Paybill: 542542  |  Account: 313126');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'payment_rejected',
            'payment_id' => $this->payment->id,
            'message'    => 'Your payment could not be verified. Reason: ' . $this->payment->admin_note,
            'mpesa_code' => $this->payment->mpesa_code,
            'amount'     => $this->payment->amount,
        ];
    }
}
```

---

## 8 — Admin manual-payments Blade view

Create `resources/views/pages/admin/manual-payments.blade.php`.

Extends `layouts.admin`. The view has two sections:

**Section A — Pending Verification queue** (table):
- Columns: Time submitted | Student name/email | Amount | M-Pesa Code | Payment type (booking/clinic) | Actions
- Each row has a **Verify** button (opens inline form with optional note textarea → POST to `admin.manual-payments.verify`) and a **Reject** button (opens inline form with required reason textarea → POST to `admin.manual-payments.reject`).
- Use `x-data` Alpine accordion to show/hide the inline form per row.

**Section B — Recently verified (last 24h)** (smaller table):
- Columns: Verified at | Student | Amount | Code | Status (success/failed badge) | Verified by | Note

Include `@csrf` in each form. Style consistent with `pages/admin/tutors.blade.php` (card, summary grid, review-panel pattern).

---

## 9 — Admin nav update

In `resources/views/layouts/admin.blade.php`, inside the **Finance** nav section, after the `Payout Requests` nav-item (line ~214), add:

```blade
<a href="{{ route('admin.manual-payments') }}" class="nav-item {{ request()->routeIs('admin.manual-payments*') ? 'active' : '' }}">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
        <rect x="9" y="3" width="6" height="4" rx="1"/>
        <path d="M9 12h6M9 16h4"/>
    </svg>
    Manual Payments
    @php $pendingManual = \App\Models\Payment::where('status','pending_verification')->count(); @endphp
    @if($pendingManual > 0)<span class="nav-badge">{{ $pendingManual }}</span>@endif
</a>
```

---

## 10 — tutors/show.blade.php wizard updates

### 10a — x-data additions (in the `x-data="{...}"` block, tab-book div)

Add these new reactive properties to the existing Alpine object:

```js
mpesaCode: '',
pendingVerification: false,
pendingPaymentId: null,
verificationMsg: 'Your booking is pending. Admin will verify your payment within 5 minutes.',
```

Remove `phone: ''`, `paymentMethod: 'mpesa'`, `stkSent: false`, `stkMsg: ''`.

### 10b — Payment step UI (Step 2, inside the payment section `@auth`)

Replace the entire payment section (payment method selector + phone input + STK banner + pay button) with:

```blade
{{-- Paybill instructions --}}
<div style="background:rgba(10,100,188,.06);border:1.5px solid rgba(10,100,188,.2);border-radius:14px;padding:18px;margin-bottom:16px;">
    <p style="font-size:13px;font-weight:700;color:#0A64BC;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-mobile-screen"></i> Pay via M-Pesa Paybill
    </p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
        <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:11px;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Paybill Number</div>
            <div style="font-family:monospace;font-size:22px;font-weight:800;color:#0A64BC;letter-spacing:.08em;">542542</div>
        </div>
        <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:11px;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Account Number</div>
            <div style="font-family:monospace;font-size:22px;font-weight:800;color:#0A64BC;letter-spacing:.08em;">313126</div>
        </div>
    </div>
    <p style="font-size:12px;color:rgba(21,40,31,.6);line-height:1.7;margin-bottom:6px;">
        1. Open <strong>M-Pesa</strong> → Lipa na M-Pesa → Paybill<br>
        2. Enter Paybill <strong>542542</strong>, Account <strong>313126</strong><br>
        3. Enter amount <strong>KSh <span x-text="price"></span></strong><br>
        4. Enter PIN and send. Copy the <strong>confirmation code</strong> (e.g. <em>QJX1234AB5</em>)
    </p>
</div>

{{-- Transaction code input --}}
<div style="margin-bottom:14px;" x-show="!isFree">
    <label style="font-size:12px;font-weight:700;color:rgba(21,40,31,.55);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px;">
        <i class="fa-solid fa-receipt"></i> M-Pesa Transaction Code
    </label>
    <input type="text" x-model="mpesaCode"
        placeholder="e.g. QJX1234AB5"
        style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:15px;font-family:monospace;outline:none;text-transform:uppercase;letter-spacing:.05em;background:#fff;"
        onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
    <p style="font-size:11px;color:rgba(21,40,31,.45);margin-top:4px;">The code is in the M-Pesa SMS you received after payment.</p>
</div>

{{-- Pay button --}}
<button @click="processPayment($data)"
    :disabled="processing || (!isFree && mpesaCode.trim().length < 6)"
    :style="isFree ? 'background:#10B981;' : 'background:#0A64BC;'"
    style="width:100%;height:52px;border-radius:999px;color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;">
    <template x-if="!processing">
        <span style="display:flex;align-items:center;gap:8px;">
            <i :class="isFree ? 'fa-solid fa-gift fa-xs' : 'fa-solid fa-paper-plane fa-xs'"></i>
            <span x-text="isFree ? 'Book Free Session' : 'Submit Payment Code'"></span>
        </span>
    </template>
    <template x-if="processing">
        <span style="display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-hourglass-half"></i> Submitting...
        </span>
    </template>
</button>
```

### 10c — Step 3: Pending state (when pendingVerification === true)

Inside `<div x-show="step === 3">`, before the existing "Booking Confirmed!" content, add:

```blade
<template x-if="pendingVerification">
<div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:40px;text-align:center;">
    <div style="width:68px;height:68px;border-radius:50%;background:rgba(238,190,21,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
        <i class="fa-solid fa-hourglass-half" style="font-size:28px;color:#EEBE15;"></i>
    </div>
    <h2 style="font-family:'Fraunces',serif;font-size:24px;color:#15281F;margin-bottom:8px;">Booking Pending Verification</h2>
    <p style="font-size:14px;color:rgba(21,40,31,.6);margin-bottom:20px;" x-text="verificationMsg"></p>
    <div style="background:#FFFBEB;border:1.5px solid #FCD34D;border-radius:14px;padding:16px;margin-bottom:24px;">
        <p style="font-size:13px;color:#92400E;line-height:1.7;">
            <i class="fa-solid fa-clock"></i> Admin verifies payments within <strong>5 minutes</strong> during business hours.<br>
            You will receive a confirmation email with your session codes once verified.
        </p>
    </div>
    <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;gap:7px;padding:13px 28px;border-radius:999px;background:#0A64BC;color:#fff;font-size:14px;font-weight:700;text-decoration:none;">
        <i class="fa-solid fa-chart-bar"></i> Go to Dashboard
    </a>
</div>
</template>

<template x-if="!pendingVerification">
    {{-- existing "Booking Confirmed!" content --}}
```

Close the `x-if="!pendingVerification"` template after the existing confirmed block's closing `</div>`.

### 10d — processPayment() JS rewrite

Replace the entire `processPayment` function:

```js
function processPayment(data) {
    if (!data || !data.date || !data.time) {
        showError('Missing Information', 'Please select a date and time.');
        return;
    }
    if (!data.isFree && data.mpesaCode.trim().length < 6) {
        showError('Missing Code', 'Please enter your M-Pesa transaction code.');
        return;
    }

    data.processing = true;

    fetch('/bookings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            tutor_profile_id: {{ $tutor->id }},
            duration_minutes: data.dur,
            scheduled_at: data.date + ' ' + data.time,
            subject_id: data.subject || null,
            notes: data.notes,
            mpesa_code: data.mpesaCode,
        })
    })
    .then(r => r.json())
    .then(res => {
        data.processing = false;
        if (res.status === 'ok' && res.free) {
            data.studentCode = res.student_code || '';
            data.bookingRef  = res.booking_uuid || '';
            data.pendingVerification = false;
            data.step = 3;
            if (res.booking_url) setTimeout(() => { window.location.href = res.booking_url; }, 3000);
            return;
        }
        if (res.status === 'pending_verification') {
            data.pendingVerification = true;
            data.pendingPaymentId    = res.payment_id || null;
            data.verificationMsg     = res.message || 'Your booking is pending admin verification.';
            data.step = 3;
            return;
        }
        showError('Booking Failed', res.message || 'Could not process booking. Please try again.');
    })
    .catch(err => {
        data.processing = false;
        console.error(err);
        showError('Network Error', 'Please try again.');
    });
}
```

---

## 11 — clinic-checkout.blade.php payment section replacement

Replace the entire `<div style="margin-bottom:24px;" x-data="{ method: 'mpesa' }">...</div>` block with:

```blade
<div x-data="{ mpesaCode: '' }">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;">Payment Instructions</h3>
    <div style="background:rgba(10,100,188,.06);border:1.5px solid rgba(10,100,188,.2);border-radius:12px;padding:16px;margin-bottom:16px;">
        <p style="font-size:13px;font-weight:700;color:#0A64BC;margin-bottom:10px;">
            <i class="fa-solid fa-mobile-screen"></i> Pay via M-Pesa Paybill
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
            <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:11px;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Paybill Number</div>
                <div style="font-family:monospace;font-size:22px;font-weight:800;color:#0A64BC;">542542</div>
            </div>
            <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:11px;color:rgba(21,40,31,.5);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Account Number</div>
                <div style="font-family:monospace;font-size:22px;font-weight:800;color:#0A64BC;">313126</div>
            </div>
        </div>
        <p style="font-size:12px;color:rgba(21,40,31,.6);line-height:1.7;">
            1. M-Pesa → Lipa na M-Pesa → Paybill<br>
            2. Paybill <strong>542542</strong>, Account <strong>313126</strong><br>
            3. Amount: <strong>KSh {{ number_format($clinic->price) }}</strong><br>
            4. Enter PIN. Copy the confirmation code from your SMS.
        </p>
    </div>

    <form id="paymentForm" method="POST" action="{{ route('clinic.pay', $clinic) }}">
        @csrf
        <div style="margin-bottom:18px;">
            <label style="font-size:13px;font-weight:700;color:var(--ink);display:block;margin-bottom:6px;">
                <i class="fa-solid fa-receipt"></i> M-Pesa Transaction Code
            </label>
            <input type="text" name="mpesa_code" x-model="mpesaCode"
                placeholder="e.g. QJX1234AB5" required
                style="width:100%;height:46px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);padding:0 14px;font-size:15px;font-family:monospace;text-transform:uppercase;letter-spacing:.05em;outline:none;box-sizing:border-box;"
                onfocus="this.style.borderColor='var(--trust-blue)'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
            @error('mpesa_code')
            <p style="font-size:12px;color:var(--coral);margin-top:5px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- User Verification --}}
        <div style="background:#F9FAFB;border-radius:10px;padding:14px;margin-bottom:20px;font-size:13px;">
            <div style="color:var(--muted);margin-bottom:6px;">Logged in as:</div>
            <div style="font-weight:600;color:var(--ink);">{{ $user->name }} ({{ $user->email }})</div>
        </div>

        {{-- Terms --}}
        <div class="terms">
            <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                <input type="checkbox" name="terms" required style="margin-top:3px;cursor:pointer;width:16px;height:16px;">
                <span>I confirm I have paid KSh {{ number_format($clinic->price) }} to Paybill 542542, Account 313126, and I agree to the <a href="#">Terms of Service</a>.</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn"
            :disabled="mpesaCode.trim().length < 6">
            <i class="fa-solid fa-paper-plane"></i> Submit Payment Code
        </button>
        <a href="{{ route('examiner-clinic') }}" class="btn btn-secondary" style="margin-top:8px;">← Back</a>
    </form>
</div>
```

---

## 12 — Routes to add

In `routes/web.php`, inside the `Route::middleware('auth')->group()`, add:

```php
// Manual payment status polling (student)
Route::get('/bookings/payment-status', [BookingController::class, 'paymentStatus'])->name('bookings.payment-status');
```

This MUST be registered **before** `Route::get('/bookings/{booking}', ...)` to avoid the `{booking}` wildcard eating `payment-status`.

In the admin route group (`Route::middleware('role:admin')->prefix('admin')->name('admin.')`), add:

```php
Route::get('/manual-payments',              [App\Http\Controllers\Admin\ManualPaymentController::class, 'index'])->name('manual-payments');
Route::post('/manual-payments/{payment}/verify', [App\Http\Controllers\Admin\ManualPaymentController::class, 'verify'])->name('manual-payments.verify');
Route::post('/manual-payments/{payment}/reject', [App\Http\Controllers\Admin\ManualPaymentController::class, 'reject'])->name('manual-payments.reject');
```

In `routes/api.php`, remove the IntaSend webhook route and the IMBankPaymentController routes are fine to leave (they do no harm).  
Add the manual payment polling route to `api.php` **only if** polling is needed from JS without full page navigation (the `paymentStatus` GET endpoint in web.php handles this for the booking wizard — no additional API route needed for MVP).

---

## 13 — Verification commands

After implementation, verify in this order:

```bash
# 1 — migration runs cleanly
php artisan migrate

# 2 — no syntax errors in new/modified PHP files
php artisan route:list --name=bookings
php artisan route:list --name=admin.manual

# 3 — no missing class references
php artisan optimize:clear

# 4 — manually test the full booking flow:
#   a) Go to /tutors/{slug} → Book Session tab
#   b) Step 1: pick session, date, time, notes → Continue
#   c) Step 2: enter code e.g. "QJX1234AB5" → Submit Payment Code
#   d) Step 3 should show the pending-verification UI (yellow hourglass)
#   e) Admin: /admin/manual-payments → Verify the payment
#   f) Check student and tutor email/notification arrive with codes

# 5 — manually test clinic checkout:
#   a) Go to /clinic/{clinic}/checkout
#   b) Enter M-Pesa code → Submit
#   c) Redirects to /clinic/my-bookings with 'info' flash
#   d) Admin verifies → ClinicEnrollment created

# 6 — test rejection flow:
#   a) Admin rejects with a reason
#   b) Student notification should arrive
#   c) Booking status becomes 'cancelled'
```
