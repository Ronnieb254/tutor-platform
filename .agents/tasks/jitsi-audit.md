# Jitsi Integration Audit Report
**Project:** Mwalimu Tutor Platform  
**Audited:** Read-only inspection of source code  
**Scope:** All Jitsi-related files — services, controllers, blade views, routes, models

---

## Executive Summary

The Jitsi integration is **partially functional** for the basic 1-on-1 booking room (it loads the Jitsi IFrame API and handles join/leave events correctly), but has **several significant bugs** that will cause real-world problems:

1. **`JITSI_USE_JWT` is missing from `.env`** — JWT auth is silently disabled; the JAAS credentials stored in `.env` are never used.
2. **JAAS JWT implementation is wrong** — when JWT is eventually enabled, it will fail because the code uses HS256 with a PEM RSA key and the wrong `sub` claim for 8x8 JaaS.
3. **`JitsiService` is missing two methods** (`generateRoomName`, `generateToken`) called by the `JoinSession` Livewire component — that component will throw a fatal error at runtime.
4. **Clinic room uses `meet.jitsi.org`** instead of `meet.jit.si` — different server, different external_api.js, and a domain mismatch.
5. **`participantLeft` fires for every remote participant leaving**, not just the local user — this prematurely triggers `markLeft` while the session is still running.
6. **Early-join window is inconsistent**: `Booking::canJoin()` opens 60 minutes before start, but `BookingController::meetingRoom()` only allows entry 15 minutes before — the "Join" button is shown when clicking it would fail.
7. **`hangup` is not a valid `JitsiMeetExternalAPI` event** — that listener is dead code.
8. **`env('JITSI_USE_JWT')` called directly in a Blade template** — returns null in production when config is cached.
9. **Clinic room has no backend attendance tracking** — the `videoConferenceJoined` handler is a stub comment.

---

## Evidence and Findings by Question

### 1. JitsiService Configuration

**File:** `app/Services/JitsiService.php`

The service reads four ENV vars via `env()` directly (not `config()`):

| ENV Var | Default | Purpose |
|---|---|---|
| `JITSI_DOMAIN` | `meet.jit.si` | Jitsi server domain |
| `JITSI_APP_ID` | `''` | API key (for JWT) |
| `JITSI_APP_SECRET` | `''` | Signing secret (for JWT) |
| `JITSI_USE_JWT` | `false` | Toggle JWT auth |

The service **does generate JWTs** via `generateJWT()` (internal) and `makeJWT()` (public), but only when `JITSI_USE_JWT` is truthy AND both `JITSI_APP_ID` and `JITSI_APP_SECRET` are non-empty.

Public methods: `createMeeting()`, `getJoinUrl()`, `getEmbedUrl()`, `makeJWT()`, `isConfigured()`.

**Missing methods:** `generateRoomName()` and `generateToken()` — these are called by `JoinSession.php` but do not exist in the service (see Bug #3 below).

---

### 2. `.env` Jitsi Settings

**File:** `.env`

```
JITSI_APP_ID="vpaas-magic-cookie-cc2d0135136f437386cca0c5abf2eb38"
JITSI_APP_SECRET="-----BEGIN PRIVATE KEY-----\nMIIEvgI...\n-----END PRIVATE KEY-----"
JITSI_DOMAIN=meet.jit.si
# JITSI_USE_JWT is ABSENT
```

**Critical finding:** `JITSI_USE_JWT` is not defined in `.env` at all. This means `env('JITSI_USE_JWT', false)` returns `false`, so `makeJWT()` always returns `null` and `isConfigured()` returns `true` (domain is set) but JWT is never applied.

The credentials present (`vpaas-magic-cookie-*` prefix) identify this as an **8x8 JaaS (Jitsi as a Service)** account, not a self-hosted Jitsi. This is important because JaaS requires a different JWT format than vanilla Jitsi (see Bug #2).

---

### 3. Where Jitsi Is Used

**PHP files:**
- `app/Services/JitsiService.php` — core service
- `app/Services/BookingService.php` — calls `$this->jitsi->getJoinUrl()` during booking confirmation
- `app/Http/Controllers/BookingController.php` — `meetingRoom()` method calls `makeJWT()`
- `app/Http/Controllers/ClinicBookingController.php` — `sessionRoom()` renders the clinic Jitsi room
- `app/Livewire/Booking/JoinSession.php` — calls non-existent `generateRoomName()` and `generateToken()`
- `app/Models/Booking.php` — `getJitsiJoinUrl()`, `getBBBJoinUrl()` (alias), `isJitsiMeetingRunning()`

**Blade views:**
- `resources/views/pages/bookings/meeting-room.blade.php` — main 1-on-1 session room
- `resources/views/pages/clinic-jitsi-room.blade.php` — clinic session room
- `resources/views/livewire/booking/join-session.blade.php` — code-entry + embedded Jitsi (legacy/unused path)
- `resources/views/pages/bookings/show.blade.php` — links to room via `route('bookings.room', $booking->uuid)`

---

### 4. `meeting-room.blade.php` Analysis

**File:** `resources/views/pages/bookings/meeting-room.blade.php`

**IFrame API embed:** Yes — correct. Loads `https://meet.jit.si/external_api.js` at line 135, instantiates `new JitsiMeetExternalAPI(domain, options)` with `parentNode: document.querySelector('#jitsi-container')`.

**JWT auth:** Conditionally included:
```blade
@if(app(\App\Services\JitsiService::class)->isConfigured() && env('JITSI_USE_JWT'))
    jwt: '{{ $jitsiJwt ?? "" }}',
@endif
```
Since `JITSI_USE_JWT` is absent from `.env`, this block never renders. When it eventually does, `env()` in a cached-config production environment returns null (see Bug #8).

**`markJoined` call:** Yes — `videoConferenceJoined` event at line 324 POSTs to `joinedUrl` (`route("bookings.joined", $booking->uuid)`). The response's `both_joined` flag starts the teaching timer.

**`markLeft` call:** Present but buggy — `participantLeft` event at line 354 calls `leftUrl` POST. This fires when **any** remote participant leaves, not just the local user (see Bug #5). `readyToClose` at line 363 correctly calls `markLeft` and redirects. The `hangup` event listener at line 369 is dead code (see Bug #7).

**CSRF token:** Present — reads from `meta[name=csrf-token]` which is provided by `@include('partials.head')`.

---

### 5. `clinic-jitsi-room.blade.php` Analysis

**File:** `resources/views/pages/clinic-jitsi-room.blade.php`

**External API URL:** `https://meet.jitsi.org/external_api.js` (line 8) — uses `meet.jitsi.org`, not `meet.jit.si`.
**JS domain:** `const domain = "meet.jitsi.org"` (line 65) — consistent with the script URL, but `meet.jitsi.org` is a different server from the booking room's `meet.jit.si`. Both are public Jitsi instances; `meet.jitsi.org` is run by the Jitsi project directly but has stricter rate limits and occasional availability issues.

**JWT auth:** Not used — no `jwt` option passed to `JitsiMeetExternalAPI`.

**Attendance tracking:** Stub only. The `videoConferenceJoined` handler logs to console and has a comment `// Mark attendance in backend if not already done` — no actual fetch call. The `videoConferenceLeft` handler is empty. No backend attendance is recorded from the room itself (enrollment `attended` flag is set in `ClinicBookingController::joinSession()` before room entry, so that part works, but session duration is not tracked).

**Leave button:** Uses a `<form method="POST" action="{{ route('logout') }}">` — this logs the user out entirely instead of just closing the Jitsi session and redirecting to a confirmation page.

---

### 6. Routes

**File:** `routes/web.php`

| Route | Name | Controller Method |
|---|---|---|
| `GET /bookings/{booking}/room` | `bookings.room` | `BookingController::meetingRoom` |
| `POST /bookings/{booking}/joined` | `bookings.joined` | `BookingController::markJoined` |
| `POST /bookings/{booking}/left` | `bookings.left` | `BookingController::markLeft` |
| `GET /bookings/{booking}/attendance` | `bookings.attendance` | `BookingController::attendanceStatus` |
| `GET /clinic/session` | `clinic.session-room` | `ClinicBookingController::sessionRoom` |
| `GET /clinic/{clinic}/join` | `clinic.join-session` | `ClinicBookingController::joinSession` |

All routes are inside the `auth` middleware group. Route names match the references in the blade templates. No missing routes found.

---

### 7. `BookingController::meetingRoom()` Analysis

**File:** `app/Http/Controllers/BookingController.php`, line ~355

The method:
1. Checks user is student, tutor, or admin — correct.
2. Checks booking status is `confirmed` — correct.
3. Checks early-join window: allows T−15 to T+(duration+30) — **functionally correct logic**, but inconsistent with `Booking::canJoin()` which opens at T−60 (see Bug #6).
4. Calls `$jitsiService->makeJWT(...)` — returns `null` because `JITSI_USE_JWT` is not set; the null is passed to the view as `$jitsiJwt`.
5. Returns `pages.bookings.meeting-room` view with `compact('booking', 'jitsiJwt')`.

The method is otherwise sound.

---

### 8. `JitsiService::makeJWT()` Analysis

**File:** `app/Services/JitsiService.php`, line 189

```php
public function makeJWT(string $roomName, string $displayName, bool $isModerator, array $options = []): ?string
{
    if (!$this->useJWT || !$this->appId || !$this->appSecret) {
        return null;
    }
    return $this->generateJWT($this->sanitizeRoomName($roomName), $displayName, $isModerator, $options);
}
```

`generateJWT()` (line ~75) creates this payload:
```php
[
    'aud'  => 'jitsi',
    'iss'  => $this->appId,     // vpaas-magic-cookie-cc2d...
    'sub'  => $this->domain,    // meet.jit.si
    'room' => $roomName,
    'exp'  => $now + 3600,
    'context' => [ 'user' => [...], 'features' => [...] ]
]
```
And signs it with: `JWT::encode($payload, $this->appSecret, 'HS256')`

**Bug:** This is **wrong for 8x8 JaaS**. The `.env` shows a JaaS account (`vpaas-magic-cookie-*`), which requires:
- Algorithm: **RS256** (not HS256)
- `kid` header: `"${APP_ID}/${KEY_ID}"` (not provided at all)
- `sub` claim: `"8x8.vc"` (not `meet.jit.si`)
- The private key must be passed as `new Key($pem, 'RS256')` to `JWT::encode()`
- The domain used in the IFrame API must be `"8x8.vc"`, not `"meet.jit.si"`

If `JITSI_USE_JWT` were set to `true`, the generated JWT would be rejected by JaaS servers.

---

### 9. Obvious Bugs Summary

| # | Bug | Location | Severity |
|---|---|---|---|
| 1 | `JITSI_USE_JWT` absent from `.env` — JWT never activates | `.env` | High |
| 2 | JWT uses HS256 + wrong `sub`/`kid` for JaaS (RS256 required) | `JitsiService::generateJWT()` | High |
| 3 | `JitsiService::generateRoomName()` and `::generateToken()` missing — fatal error | `JoinSession.php` line 43–44 | High (that code path crashes) |
| 4 | Clinic room uses `meet.jitsi.org` not `meet.jit.si` — domain mismatch | `clinic-jitsi-room.blade.php` line 8, 65 | Medium |
| 5 | `participantLeft` calls `markLeft` for remote participant leaving, not local | `meeting-room.blade.php` line 354 | Medium |
| 6 | `Booking::canJoin()` opens at T−60, controller rejects before T−15 | `Booking.php` line 102 / `BookingController.php` line 372 | Medium |
| 7 | `addEventListener('hangup')` is dead code — not a real external API event | `meeting-room.blade.php` line 369 | Low |
| 8 | `env('JITSI_USE_JWT')` in Blade returns null in production with cached config | `meeting-room.blade.php` line 263 | Low (currently masked by bug #1) |
| 9 | Clinic room `videoConferenceJoined` has no backend attendance POST — stub only | `clinic-jitsi-room.blade.php` line 122 | Medium |
| 10 | Clinic "Leave Session" button fires logout, not just room exit | `clinic-jitsi-room.blade.php` line 48–51 | Medium |
| 11 | `meeting_url` stored in DB is a plain Jitsi URL (not a JaaS URL), inconsistent with JaaS app ID | `BookingService::confirmBooking()` line ~96 | Low |

---

### 10. 15-Minute Early-Join Window

**Controller logic** (`BookingController::meetingRoom()`, line 365–377):

```php
$minutesUntilStart = $now->diffInMinutes($scheduledTime, false);
$minutesSinceStart = $scheduledTime->diffInMinutes($now, false);
$canJoin = ($minutesUntilStart <= 15) && ($minutesSinceStart <= ($booking->duration_minutes + 30));
```

The Carbon logic is correct: allows entry from T−15 to T+(duration+30). However, `Booking::canJoin()` in the model (`Booking.php` line 96–105) opens the window at T−60:

```php
$opensAt = $this->scheduled_at->copy()->subMinutes(60);
$closesAt = $this->scheduled_at->copy()->addMinutes($this->duration_minutes + 30);
return now()->between($opensAt, $closesAt);
```

`show.blade.php` calls `$booking->canJoin()` to show/hide the "Join Session" button. This means:
- The Join button **appears** at T−60
- But **clicking** it between T−60 and T−15 redirects back to the booking page with an error: "You can only join the session 15 minutes before it starts."

This inconsistency creates a confusing UX: users see an active "Join" button but get rejected.

---

### 11. Session Timer / Attendance Tracking

**1-on-1 booking room** — well-implemented:
- `videoConferenceJoined` fires `POST /bookings/{uuid}/joined` → `markJoined()` records `tutor_joined`, `tutor_joined_at` / `student_joined_at`
- JS polls `GET /bookings/{uuid}/attendance` every 10 seconds → `attendanceStatus()` returns both-joined state and elapsed seconds
- Teaching timer starts when `both_joined` is true, driven by server-elapsed calculation
- `Booking::classifyDelivery()` compares `actual_duration_seconds` against 80% threshold for earnings/refund classification
- `participantLeft` (bugged — fires for remote leaves too) and `readyToClose` POST to `markLeft()` which calculates actual session seconds

**Clinic room** — not implemented:
- `videoConferenceJoined` is a console.log + comment stub (line 121–124)
- `videoConferenceLeft` is an empty handler (line 126–128)
- No fetch calls to any backend endpoint from the room

---

## Conclusions and Recommendations

### Immediate fixes required

**Fix 1 — Add `JITSI_USE_JWT` to `.env` (when ready to enable JWT):**
```
JITSI_USE_JWT=true
```
But do NOT enable this until Bug #2 is also fixed (wrong JWT format).

**Fix 2 — Rewrite `generateJWT()` for JaaS (RS256, correct claims):**
```php
protected function generateJWT(string $roomName, string $displayName, bool $isModerator, array $options = []): string
{
    $now = time();
    $keyId = $this->appId . '/some-kid'; // derive from your JaaS API key page
    $payload = [
        'aud'     => 'jitsi',
        'iss'     => $this->appId,
        'sub'     => '8x8.vc',          // MUST be 8x8.vc for JaaS
        'room'    => $roomName,
        'exp'     => $now + (int) env('JITSI_JWT_TTL', 3600),
        'nbf'     => $now,
        'context' => [
            'user' => [
                'name'      => $displayName,
                'email'     => $options['email'] ?? '',
                'moderator' => $isModerator,
            ],
        ],
    ];
    $header = ['kid' => $keyId]; // Required for JaaS
    return JWT::encode($payload, new \Firebase\JWT\Key($this->appSecret, 'RS256'), 'RS256', $keyId);
}
```
Also update the IFrame domain to `8x8.vc` and load `external_api.js` from `https://8x8.vc/external_api.js` when using JaaS.

**Fix 3 — Add missing methods to `JitsiService`** (or remove the `JoinSession` Livewire component if it's the old code path no longer used):
```php
public function generateRoomName(Booking $booking): string
{
    return $this->sanitizeRoomName($booking->meeting_room);
}

public function generateToken(User $user, string $roomName, bool $isModerator): ?string
{
    return $this->makeJWT($roomName, $user->name, $isModerator, ['email' => $user->email]);
}
```

**Fix 4 — Standardize clinic room domain.** Change `clinic-jitsi-room.blade.php` to use `meet.jit.si` (matching the booking room and `.env`):
```html
<script src="https://meet.jit.si/external_api.js"></script>
...
const domain = "meet.jit.si";
```

**Fix 5 — Fix `participantLeft` handler.** It should NOT call `markLeft` — that endpoint is for the local user leaving. Only `readyToClose` should trigger it:
```js
// Remove the fetch() call inside participantLeft; keep only pollAttendance()
api.addEventListener('participantLeft', () => { pollAttendance(); });
```

**Fix 6 — Align early-join windows.** Either update `Booking::canJoin()` to use 15 minutes, or update `BookingController::meetingRoom()` to use 60 minutes. Recommended: match the model (60 min) so the UI and the gate agree:
```php
// In meetingRoom()
$canJoin = ($minutesUntilStart <= 60) && ($minutesSinceStart <= ($booking->duration_minutes + 30));
```

**Fix 7 — Remove dead `hangup` event listener** in `meeting-room.blade.php`:
```js
// DELETE these lines:
api.addEventListener('hangup', () => {
    window.location.href = bookingUrl;
});
```
The `readyToClose` event already handles the redirect.

**Fix 8 — Replace `env()` in Blade with a config value:**
```php
// In config/services.php or new config/jitsi.php:
'jitsi_use_jwt' => env('JITSI_USE_JWT', false),
```
```blade
@if(app(\App\Services\JitsiService::class)->isConfigured() && config('services.jitsi_use_jwt'))
```

**Fix 9 — Implement clinic room attendance backend call:**
```js
jitsiAPI.addEventListener('videoConferenceJoined', function() {
    fetch('/clinic/{{ $clinic->id }}/attendance-joined', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content }
    });
});
```
This requires adding the route and a controller method, plus a `meta[name=csrf-token]` tag in the clinic room head (currently missing).

**Fix 10 — Fix clinic room Leave button.** Replace logout with a redirect:
```blade
<a href="{{ route('examiner-clinic') }}" class="exit-btn">
    <i class="fa-solid fa-right-from-bracket"></i> Leave Session
</a>
```
Or call `jitsiAPI.executeCommand('hangup')` then redirect via JS without logging the user out.

### What works correctly

- The booking meeting room loads Jitsi IFrame API correctly when meeting_room is populated.
- `videoConferenceJoined` → `markJoined()` → attendance polling → teaching timer chain works correctly for 1-on-1 sessions.
- `readyToClose` → `markLeft()` → `actual_duration_seconds` accumulation works.
- JWT is generated with correct `firebase/php-jwt` library (v7+, installed).
- Clinic session token-based access control (encrypt/decrypt, expiry check, user id check) is solid.
- All booking room routes are correctly named and referenced.
- CSRF tokens are correctly threaded through all fetch() calls in the booking room.
- `BookingService::confirmBooking()` sets `meeting_room` and `meeting_url` reliably.
