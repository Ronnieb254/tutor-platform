# Session Codes — Gaps Investigation Report

## Summary

The infrastructure for session codes is partially in place but has **three critical gaps**:

1. **No `tutor_code` column** exists on `examiner_clinics` — the tutor has only the shared `session_code` (the Jitsi room name), not a private per-tutor access code.
2. **No email/notification sent to the tutor** when they are assigned to a group session or clinic (either on creation or on tutor update).
3. **No email/notification sent to the student** with their unique `session_code` after a successful M-Pesa (Daraja) payment — the code is generated and stored, but never emailed.

The Paystack path shows a flash message with the code on redirect, but Daraja (the primary payment path) silently creates the enrollment with no notification at all.

---

## Evidence

### 1. `ExaminerClinic` model — columns

**File:** `app/Models/ExaminerClinic.php`

`$fillable` includes `session_code` but **not** `tutor_code`:

```php
protected $fillable = [
    'tutor_id','subject_id','curriculum_id','paper_type','parent_clinic_id','batch_number',
    'title','name','description','topics_covered',
    'scheduled_at','duration_minutes','price','max_students','status','meeting_room',
    'created_by','session_code','recording_url',
    'is_auto_created',
    'is_group_session','series_id','session_number','total_sessions',
];
```

The `booted()` callback auto-generates `session_code` on every clinic/group-session create:
```php
$clinic->session_code = $clinic->session_code ?? ('CLINIC-' . strtoupper(Str::random(6)));
```

`GroupSessionController::store()` also explicitly sets:
```php
$validated['session_code'] = 'GRP-' . strtoupper(Str::random(8));
```

**Finding:** `session_code` on `examiner_clinics` is the Jitsi *room name* (shared by everyone in the session), not a private tutor code. There is no separate `tutor_code` column anywhere in the model or in any migration.

---

### 2. `ClinicEnrollment` model — columns

**File:** `app/Models/ClinicEnrollment.php`

```php
protected $fillable = ['clinic_id','student_id','payment_id','session_code','attended'];
```

**Migration** `2024_01_01_000014_add_mwalimu_fields.php` creates `clinic_enrollments` with:
```php
$table->string('session_code', 20)->nullable()->unique();
```

**Finding:** `session_code` column **exists** in `clinic_enrollments` and is populated at enrollment time. Each student gets a unique 8-character code. ✅

---

### 3. Code generation — Daraja payment path

**File:** `app/Http/Controllers/DarajaCallbackController.php`, method `confirmRelatedRecord()`

On a successful Daraja callback the enrollment is created:
```php
$enrollment = \App\Models\ClinicEnrollment::create([
    'clinic_id'    => $clinic->id,
    'student_id'   => $user->id,
    'payment_id'   => $payment->id,
    'session_code' => strtoupper(\Illuminate\Support\Str::random(8)),
]);
```

**After this `create()` call there are zero notification or email calls.** The `Log::info()` records the enrollment ID but nothing is sent to the student.

The `status()` poll endpoint (`/api/daraja/status`) returns `student_code` only for 1-on-1 bookings:
```php
'student_code' => $booking?->fresh()?->session_code_student,
```
It does **not** return the clinic enrollment code.

---

### 4. Code generation — Paystack / manual path

**File:** `app/Http/Controllers/PaystackCallbackHelper.php`, method `confirmClinicEnrollment()`

Also creates the enrollment with a random 8-char code:
```php
$enrollment = ClinicEnrollment::create([
    'clinic_id'    => $clinic->id,
    'student_id'   => $user->id,
    'payment_id'   => $payment->id,
    'session_code' => strtoupper(Str::random(8)),
]);
```

Here the code **is** shown in the flash message on redirect:
```php
return redirect($route)->with('success', "Booking confirmed! Your session code: {$enrollment->session_code}. ...");
```

But it is only a flash message — **no email is sent**. If the user navigates away before reading it, the code is lost until they visit "My Bookings".

---

### 5. Tutor assignment — group sessions

**File:** `app/Http/Controllers/Admin/GroupSessionController.php`, `store()` method

The `tutor_id` is accepted as an optional field and saved. No notification call exists anywhere in `GroupSessionController`. Search of the file for `notif`, `email`, `send` returns zero matches (confirmed by grep).

**Finding:** When admin creates a group session and assigns a tutor, the tutor receives **no email, no database notification, and no code**.

---

### 6. Tutor assignment — examiner clinics

**File:** `app/Http/Controllers/Admin/ExaminerClinicController.php`, `store()` method

The clinic is created without a `tutor_id` field in validation (tutors are not assigned at create time for clinics). The only notification-related code in this file is a TODO comment on the `cancel()` method:
```php
// TODO: Send notifications to all enrolled students
```

**Finding:** No tutor assignment notification exists. ✅ Note: clinics do generate a `session_code` via `booted()` and the explicit `'EXAM-' . strtoupper(Str::random(8))` in `store()`, but it is never communicated to the tutor.

---

### 7. Notification classes inventory

**Directory:** `app/Notifications/`

Existing classes:
- `BookingConfirmedStudent.php` — for 1-on-1 bookings ✅
- `BookingConfirmedTutor.php` — for 1-on-1 bookings ✅
- `BookingCancelledNotification.php`
- `BookingConfirmedNotification.php`
- `BookingCreatedNotification.php`
- `SessionCompletedStudent.php`
- `SessionCompletedTutor.php`
- `SessionReminderNotification.php`
- `TutorApproved.php`
- `UserJoinedSessionNotification.php`
- (others unrelated to sessions)

**Missing:**
- `ClinicEnrollmentConfirmed.php` (student receives their unique session code after payment)
- `ClinicTutorAssigned.php` (tutor receives the session code when assigned to a clinic/group session)
- `GroupSessionEnrollmentConfirmed.php` (or reuse `ClinicEnrollmentConfirmed` with a flag)

---

### 8. 1-on-1 bookings (reference — working correctly)

**File:** `app/Services/BookingService.php`, `confirmBooking()`

Generates two codes and saves them:
```php
$tutorCode   = 'TUT-' . strtoupper(Str::random(6));
$studentCode = 'STU-' . strtoupper(Str::random(6));
...
$booking->update([
    'session_code_tutor'   => $tutorCode,
    'session_code_student' => $studentCode,
]);
```
Then immediately sends both `BookingConfirmedStudent` and `BookingConfirmedTutor` notifications (via `Notification::sendNow()`). This is the complete, correct pattern. ✅

---

## Conclusions and Recommendations

### What is missing

| Gap | Severity | Detail |
|-----|----------|--------|
| No `tutor_code` column on `examiner_clinics` | High | The tutor has no private access code; they share the public `session_code` room name |
| No tutor notification on group session creation | High | `GroupSessionController::store()` assigns a tutor but sends nothing |
| No tutor notification on clinic creation | High | `ExaminerClinicController::store()` creates the clinic's `session_code` but no notification |
| No student notification after Daraja enrollment | High | `DarajaCallbackController::confirmRelatedRecord()` creates enrollment silently |
| No student notification after Paystack enrollment | Medium | `PaystackCallbackHelper::confirmClinicEnrollment()` only shows a flash message |
| No `session_code` in Daraja poll endpoint for clinic | Medium | `/api/daraja/status` returns booking code but not enrollment code |
| No `ClinicEnrollmentConfirmed` notification class | High | Class does not exist; must be created |
| No `ClinicTutorAssigned` notification class | High | Class does not exist; must be created |

### Recommended fixes (do not implement — for planning only)

1. **Add `tutor_code` column to `examiner_clinics`** via a new migration. Generate it in `ExaminerClinic::booted()` alongside `session_code` using a `TUT-XXXXXXXX` prefix.

2. **Create `ClinicTutorAssigned` notification** (channels: `mail` + `database`). Send it from both `GroupSessionController::store()` and `GroupSessionController::update()` (if tutor changes) and from `ExaminerClinicController` wherever a `tutor_id` is set/changed. The notification mail should include: session title, date/time, the clinic `session_code` (room name), and the tutor's private `tutor_code`.

3. **Create `ClinicEnrollmentConfirmed` notification** (channels: `mail` + `database`). Send it from `DarajaCallbackController::confirmRelatedRecord()` immediately after `ClinicEnrollment::create()`, and from `PaystackCallbackHelper::confirmClinicEnrollment()` after the same. The notification mail should include: session title, date/time, and the student's unique `enrollment->session_code`.

4. **Update `DarajaCallbackController::status()`** to include the enrollment code when `payment_type` is `clinic` or `group_session`, so the frontend polling page can display it.

5. **Follow the `BookingService::confirmBooking()` pattern** exactly: generate codes, save, then call `Notification::sendNow()` outside the DB transaction.
