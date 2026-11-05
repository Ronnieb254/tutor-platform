# Implementation Plan — Full Booking Session Lifecycle

> Authoritative reference for all coder steps. When in conflict, the original user messages win.
> Worktree root: `c:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform`
> All paths below are relative to that root unless stated otherwise.

---

## Codebase Findings (audit summary)

### What already exists and MUST NOT be broken
- `BookingController` — `store`, `confirm`, `forceConfirm`, `cancel`, `markJoined`, `meetingRoom`, `getAvailableSlots`
- `BookingService::confirmBooking` — generates TUT/STU codes, Jitsi URL, sends `BookingConfirmedStudent` / `BookingConfirmedTutor` notifications
- Both notification classes send `database` + `mail` channels; email views at `resources/views/emails/booking-confirmed-{student,tutor}.blade.php`
- `JitsiService` — builds Jitsi URL; JWT support exists but **`JITSI_USE_JWT` is not set in `.env`** so JWT is off; the `JITSI_APP_ID` / `JITSI_APP_SECRET` in `.env` are real JaaS (8x8) credentials (not plain meet.jit.si)
- `meeting-room.blade.php` embeds Jitsi via `JitsiMeetExternalAPI` without JWT; no attendance events wired
- `ConfirmPaidBookings` artisan command — handles M-Pesa confirm loop; scheduled in `routes/console.php` (not yet)
- `RefundService` — credits student wallet; processes approved refunds to `wallets` table
- `Wallet` model has `.credit()` and `.debit()` methods; `wallet_transactions` table exists
- `Booking` model `status` enum: `pending | confirmed | cancelled | completed | no_show` — **`partial` is missing**
- `Booking` model booleans `student_joined` / `tutor_joined` — exist but are bare booleans, **no timestamps**
- `BookingService::confirmBooking` **credits tutor wallet at confirmation time** — this is wrong for the lifecycle; tutor earnings should be credited at session completion after delivery check

### Critical gaps identified

| Gap | Stage | Impact |
|-----|-------|--------|
| No `tutor_joined_at` / `student_joined_at` timestamp columns | 4, 5, 7 | Can't measure actual session duration |
| No `actual_start_at` / `actual_end_at` columns | 5, 6, 7 | Can't compute delivery % |
| `status` enum missing `partial` value | 7 | Can't record partial delivery |
| `Booking::canJoin()` uses 60 min window; task requires 5 min window | 2, 3 | Wrong access gate |
| Jitsi IFrame `participantJoined`/`participantLeft` postMessage events not wired | 4 | Attendance not tracked |
| No `POST /bookings/{booking}/attendance` endpoint | 4 | Attendance events have nowhere to land |
| JWT not generated for Jitsi embed; `JITSI_USE_JWT=true` not set | 3 | No moderator enforcement |
| `JitsiService::generateJWT` has wrong `aud` field (`appId` instead of `"jitsi"`) and `moderator` is string `'true'`/`'false'` not boolean | 3 | Jitsi JWT auth broken |
| No `CompleteExpiredSessions` artisan command | 7 | Sessions never auto-complete |
| No `SessionReviewNotification` / `SessionCompletedNotification` | 8 | Parties not prompted post-session |
| `BookingService::confirmBooking` credits tutor wallet at confirm time | 8 | Tutor paid before session delivered |
| No delivery-check logic (80% threshold) | 7 | No partial/no_show determination |
| `bookings.show` countdown uses 60-min window text | 2 | Shows wrong window |
| `meeting-room.blade.php` has no server-side attendance ping | 4 | Join events go nowhere |
| No review-prompt section in `bookings.show` for tutor after completion | 8 | Tutor can't leave review |
| `routes/console.php` schedules nothing | — | Artisan commands never run |

---

## Implementation Plan

- [ ] 1. **Migration — add session lifecycle columns to `bookings` table**
      Add: `tutor_joined_at` (nullable timestamp), `student_joined_at` (nullable timestamp), `actual_start_at` (nullable timestamp), `actual_end_at` (nullable timestamp), `delivery_status` (nullable string: `full|partial|not_delivered`), and alter the `status` enum to include `partial`.
      Files:
      - `database/migrations/2026_11_10_000001_add_lifecycle_columns_to_bookings.php` ← CREATE
      Verify: `php artisan migrate --pretend` exits 0; then `php artisan migrate`.

- [ ] 2. **Update `Booking` model — fillable, casts, helpers**
      Add new columns to `$fillable` and `$casts`. Add helper methods:
      - `scheduledEndAt()` → `Carbon`: `scheduled_at + duration_minutes`
      - `isWithin5Minutes()` → bool: `now()` is within 5 min before start
      - `isRoomOpen()` → bool: `now()` is between `scheduled_at - 5min` and `scheduledEndAt() + 30min`
      - Replace `canJoin()` to use `isRoomOpen()` (5-min window, not 60-min)
      - `deliveredMinutes()` → float: difference in minutes between `actual_start_at` and `actual_end_at` (null-safe, returns 0)
      - `deliveryPercent()` → float: `deliveredMinutes() / duration_minutes * 100`
      Files:
      - `app/Models/Booking.php` ← MODIFY
      Verify: `php artisan tinker` — `App\Models\Booking::first()->isRoomOpen()` executes without error.

- [ ] 3. **Fix `JitsiService::generateJWT` — correct JWT payload**
      The existing `generateJWT` has two bugs: `aud` is set to `$this->appId` (must be `"jitsi"`); `moderator` context field is a string `'true'`/`'false'` (must be PHP `true`/`false` boolean). Fix both. Also add `JITSI_USE_JWT=true` to `.env` so the service produces signed tokens.
      The corrected payload shape:
      ```json
      {
        "context": { "user": { "name": "...", "email": "...", "moderator": true } },
        "aud": "jitsi",
        "iss": "<JITSI_APP_ID>",
        "sub": "<JITSI_DOMAIN>",
        "room": "<room_name>",
        "exp": now+3600
      }
      ```
      Note: the `sub` field must be the Jitsi domain (`meet.jit.si`). The existing service has no `sub` field — add it.
      Files:
      - `app/Services/JitsiService.php` ← MODIFY (`generateJWT` method only)
      - `.env` ← MODIFY: add `JITSI_USE_JWT=true`
      Verify: `php artisan tinker` — `app(\App\Services\JitsiService::class)->getJoinUrl('test-room','Test User',true,['email'=>'a@b.com'])` returns a URL containing `?jwt=`.

- [ ] 4. **Update `meeting-room.blade.php` — JWT token injection + attendance events**
      - Generate a signed JWT server-side in the `BookingController::meetingRoom` method and pass it as `$jwtToken` to the view
      - In the Jitsi `options`, add `jwt: "{{ $jwtToken }}"` to the options object
      - Wire `participantJoined` and `participantLeft` IFrame API events to `POST /bookings/{uuid}/attendance` with JSON `{ event: 'joined'|'left', participant: { id, displayName } }`
      - Add a visible teaching timer `<div id="teaching-timer">` that starts counting up from `00:00` once both parties are detected joined (track via `participantJoined` count ≥ 2)
      - Show a "Session Ending Soon" countdown banner when `now() > scheduledEndAt - 5min`
      Files:
      - `resources/views/pages/bookings/meeting-room.blade.php` ← MODIFY
      - `app/Http/Controllers/BookingController.php` ← MODIFY (`meetingRoom` method: generate JWT, pass to view)
      Verify: Open meeting room URL in browser — no JS console errors; Jitsi loads with JWT in URL.

- [ ] 5. **Add `POST /bookings/{booking}/attendance` route + controller method**
      New controller method `BookingController::recordAttendance(Request $request, Booking $booking)`:
      - Validate `event` (in: `joined`, `left`) and `participant.id`
      - Determine if the participant is the tutor or student based on their `session_code_tutor` / `session_code_student` (passed as `participant.code` in the JS call)
      - On `joined`:
        - Set `tutor_joined_at = now()` if tutor, else `student_joined_at = now()`
        - Set boolean `tutor_joined = true` / `student_joined = true`
        - When **both** `tutor_joined_at` and `student_joined_at` are set and `actual_start_at` is null → set `actual_start_at = now()`
      - On `left`:
        - Set `actual_end_at = now()` (overwrite on each leave; final value captured by the complete command)
      - Return `{ status: 'ok' }`
      Add route: `Route::post('/bookings/{booking:uuid}/attendance', [BookingController::class, 'recordAttendance'])->name('bookings.attendance');`
      Files:
      - `app/Http/Controllers/BookingController.php` ← MODIFY (add `recordAttendance` method)
      - `routes/web.php` ← MODIFY (add route inside auth group)
      Verify: `php artisan route:list | grep attendance` shows the route.

- [ ] 6. **Update `bookings.show` — 5-minute access window, countdown, and session stage UI**
      The existing view uses a 60-minute window in messaging and the `canJoin()` check. Change:
      - Replace all "60 minutes" copy with "5 minutes before your session"
      - The countdown `#countdown` should show "Opens in X min" based on `scheduled_at - 5min`, not `scheduled_at - 60min`
      - Add a "Session In Progress" panel (visible when `actual_start_at` is set and `actual_end_at` is null): shows a live teaching timer (JS, counts up from `actual_start_at`), and remaining time vs. booked duration
      - Add a "Session Ending Soon" panel: visible in the last 5 minutes of booked duration + 5-minute buffer
      - For `status = 'partial'` show a yellow "Partially Delivered" panel with refund status
      - For `status = 'no_show'` show a red "Tutor No-Show" panel with refund status
      Files:
      - `resources/views/pages/bookings/show.blade.php` ← MODIFY
      Verify: Visit `/bookings/{uuid}` — page renders without Blade errors for all status values.

- [ ] 7. **Fix `BookingService::confirmBooking` — remove premature tutor wallet credit**
      The current code credits `tutor_earning` to the tutor's wallet inside `confirmBooking`. This must be removed — earnings are credited only after the session completes with `full` or `partial` delivery (handled by the new `CompleteExpiredSessions` command). The `Wallet::firstOrCreate` and `wallet->credit()` block inside the DB transaction must be deleted. Leave everything else intact.
      Files:
      - `app/Services/BookingService.php` ← MODIFY (remove wallet credit from `confirmBooking`)
      Verify: `php artisan tinker` — calling `confirmBooking` on a test booking no longer creates a `wallet_transactions` row.

- [ ] 8. **New Artisan command: `sessions:complete-expired`**
      Create `app/Console/Commands/CompleteExpiredSessions.php`:
      - Signature: `sessions:complete-expired`
      - Description: Auto-complete sessions past their end time + 30 min buffer; determine delivery status; credit tutor wallet; process student refunds where applicable
      - Query: `confirmed` bookings where `scheduled_at + duration_minutes + 30 minutes <= now()`
      - For each booking:
        - Compute `deliveryPercent()` from `actual_start_at` / `actual_end_at` (or 0 if null)
        - **Full delivery** (`both joined AND deliveryPercent >= 80`): set `status = completed`, `delivery_status = full`, `completed_at = now()`, credit `tutor_earning` to tutor wallet
        - **Partial delivery** (`tutor joined but student no-show OR 0 < deliveryPercent < 80`): set `status = partial`, `delivery_status = partial`, `completed_at = now()`, credit 50% of `tutor_earning` to tutor wallet, create `Refund` record (50% of `total_amount`, reason `'other'`, status `'approved'`) → call `RefundService::processRefund`
        - **Not delivered** (`tutor did NOT join`): set `status = no_show`, `delivery_status = not_delivered`, `completed_at = now()`, do NOT credit tutor, create `Refund` (100% of `total_amount`, reason `'tutor_cancelled'`, status `'approved'`) → call `RefundService::processRefund`
        - Fire `SessionCompletedNotification` to both parties (see item 9)
      Files:
      - `app/Console/Commands/CompleteExpiredSessions.php` ← CREATE
      Verify: `php artisan sessions:complete-expired` runs without fatal errors.

- [ ] 9. **New notification: `SessionCompletedNotification`**
      Create `app/Notifications/SessionCompletedNotification.php`:
      - Constructor: `(Booking $booking, User $notifiable_user)`
      - Channels: `['database', 'mail']`
      - `toMail`: subject "Your session has ended – Please leave a review"; body includes session details, delivery status, and a CTA link to `route('bookings.show', $booking->uuid)` with `#review` anchor
      - `toArray`: type `'session_completed'`, booking uuid, delivery_status, url
      Create email view `resources/views/emails/session-completed.blade.php` — match the style of existing booking email views (inline CSS, Mwalimu branding, dark header).
      Files:
      - `app/Notifications/SessionCompletedNotification.php` ← CREATE
      - `resources/views/emails/session-completed.blade.php` ← CREATE
      Verify: `php artisan tinker` — `new App\Notifications\SessionCompletedNotification($booking, $user)` instantiates without error.

- [ ] 10. **Update `refunds` table — add missing `reason` enum value `'no_show'`**
       The existing `refunds.reason` enum has `first_20min_guarantee | tutor_cancelled | technical_issue | quality_issue | other`. The auto-complete command uses `'tutor_cancelled'` for no-shows which already exists, so the enum is fine. However, verify the `Refund` model's `$fillable` includes all needed fields. Add `'no_show'` to the enum as a cleaner value for the auto-complete command.
       Files:
       - `database/migrations/2026_11_10_000002_add_no_show_to_refunds_reason.php` ← CREATE
       - `app/Console/Commands/CompleteExpiredSessions.php` ← use `'no_show'` reason (coordinate with item 8)
       Verify: `php artisan migrate --pretend` exits 0.

- [ ] 11. **Schedule the two Artisan commands in `routes/console.php`**
       Register both commands in the Laravel 11 schedule (uses `routes/console.php`):
       - `bookings:confirm-paid` → every minute
       - `sessions:complete-expired` → every 5 minutes
       Files:
       - `routes/console.php` ← MODIFY
       Verify: `php artisan schedule:list` shows both commands with their frequencies.

- [ ] 12. **Tutor review section in `bookings.show`**
       The existing view only shows the review form for students. Add an equivalent tutor review section for when `$isTutor && $booking->status === 'completed'`. The tutor review is stored in the same `Review` model but as a `tutor_review` (check if `Review` has a `reviewer_id` or similar — if not, use a separate `tutor_feedback` column approach: simpler, add `tutor_feedback` and `tutor_rating` to `bookings`).
       Decision: Add `tutor_feedback` (nullable text) and `tutor_rating` (nullable tinyint) directly on `bookings` to avoid a second reviews table row. Create a migration for these columns. Add a POST endpoint `bookings/{booking}/tutor-feedback` that stores them.
       Files:
       - `database/migrations/2026_11_10_000003_add_tutor_feedback_to_bookings.php` ← CREATE
       - `app/Models/Booking.php` ← MODIFY (add to `$fillable`)
       - `resources/views/pages/bookings/show.blade.php` ← MODIFY (add tutor feedback form)
       - `routes/web.php` ← MODIFY (add `POST /bookings/{booking}/tutor-feedback` route)
       Verify: Tutor feedback form renders for completed bookings; POST saves successfully.

- [ ] 13. **Fix `BookingController::meetingRoom` — tighten access to 5-minute window**
       The current implementation allows entry 15 minutes before. Change the `$minutesUntilStart <= 15` check to `<= 5` and update the error message to match.
       Files:
       - `app/Http/Controllers/BookingController.php` ← MODIFY (`meetingRoom` method)
       Verify: Attempt to access room >5 min before start returns redirect with correct error.

- [ ] 14. **Wire `bookings.show` countdown to 5-minute room open, not 60 minutes**
       The JS countdown in `bookings.show` currently shows `Opens in X min` based on `$minutesUntil` which is `now()->diffInMinutes($sessionStart)`. Change so the countdown target is `scheduled_at - 5 minutes` (room open time), and the "opens in" text reflects this.
       Files:
       - `resources/views/pages/bookings/show.blade.php` ← MODIFY (JS countdown logic — this is the same file as items 6 and 12, batch together)
       Verify: Refresh page — countdown shows minutes until `scheduled_at - 5min`.

---

## Files Created Summary

| File | Action |
|------|--------|
| `database/migrations/2026_11_10_000001_add_lifecycle_columns_to_bookings.php` | CREATE |
| `database/migrations/2026_11_10_000002_add_no_show_to_refunds_reason.php` | CREATE |
| `database/migrations/2026_11_10_000003_add_tutor_feedback_to_bookings.php` | CREATE |
| `app/Console/Commands/CompleteExpiredSessions.php` | CREATE |
| `app/Notifications/SessionCompletedNotification.php` | CREATE |
| `resources/views/emails/session-completed.blade.php` | CREATE |
| `app/Models/Booking.php` | MODIFY |
| `app/Services/BookingService.php` | MODIFY |
| `app/Services/JitsiService.php` | MODIFY |
| `app/Http/Controllers/BookingController.php` | MODIFY |
| `resources/views/pages/bookings/show.blade.php` | MODIFY |
| `resources/views/pages/bookings/meeting-room.blade.php` | MODIFY |
| `routes/web.php` | MODIFY |
| `routes/console.php` | MODIFY |
| `.env` | MODIFY |

## Missing items confirmed absent (must be created)

- `CompleteExpiredSessions` command — absent; `ConfirmPaidBookings` exists
- `SessionCompletedNotification` — absent; `SessionReminderNotification` exists but unused for this
- `sessions-completed.blade.php` email view — absent
- Migration columns: `tutor_joined_at`, `student_joined_at`, `actual_start_at`, `actual_end_at`, `delivery_status` — all absent
- `POST /bookings/{booking}/attendance` route — absent
- `BookingController::recordAttendance` — absent
- Tutor feedback columns on bookings — absent
- Schedule registration in `routes/console.php` — absent
- JWT enabled in `.env` (`JITSI_USE_JWT`) — absent

## Do NOT touch

- `DarajaService` and `DarajaCallbackController` (M-Pesa flow)
- `BookingController::store` (wizard payment flow)
- `BookingWizard` Livewire component (not in this plan)
- `BookingConfirmedStudent` / `BookingConfirmedTutor` notification classes
- `ConfirmPaidBookings` artisan command
- Any existing migration files
- `RefundService` (use as-is, call it from the new command)
