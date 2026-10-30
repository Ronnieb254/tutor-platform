# Implementation Plan: Security Features (FEAT-002 to FEAT-005)

This plan implements 4 security features for the Mwalimu Laravel 12 tutoring platform: Two-Factor Authentication (2FA), Enhanced Password Reset, Login Activity Tracking, and Session Management.

**Note:** This work has been decomposed into FEAT artifacts under `.agents/tasks/security-features/features/`. The FEAT-based workflow is the primary implementation path. This markdown file serves as a fallback reference.

---

## Context Summary

- **Project:** Laravel 12 + Livewire 3 tutoring platform (PHP 8.4, MySQL 8.0+)
- **Auth:** Standard Laravel Auth with LoginController, User model with role enum (student/tutor/admin/parent)
- **Sessions:** Database driver (sessions table exists)
- **Test Framework:** None installed (no Pest/PHPUnit) - skip writing tests
- **Verification:** Manual testing via browser + checking Laravel logs
- **Existing Patterns:** Controllers use type-hinted Request, Notifications via database+mail, Services in app/Services/, Blade views with inline CSS custom properties

---

## Feature Order & Dependencies

Features are independent of each other but share some common patterns. Recommended implementation order:

1. **FEAT-002:** Two-Factor Authentication (2FA) - modifies LoginController
2. **FEAT-003:** Enhanced Password Reset - extends password reset flow
3. **FEAT-004:** Login Activity Tracking - hooks into LoginController (works with FEAT-002)
4. **FEAT-005:** Session Management - adds middleware for all requests

---

## FEAT-002: Two-Factor Authentication (2FA)

### Files to Create

- `database/migrations/2026_10_31_000001_create_two_factor_auth_table.php`
- `app/Models/TwoFactorAuth.php`
- `app/Http/Controllers/Auth/TwoFactorController.php`
- `resources/views/auth/2fa-setup.blade.php`
- `resources/views/auth/2fa-challenge.blade.php`
- `resources/views/auth/2fa-force.blade.php`
- `resources/views/pages/settings/security.blade.php` (or add to existing)
- `app/Notifications/TwoFactorEnabledNotification.php`

### Files to Modify

- `app/Models/User.php` - add twoFactorAuth() relationship and has2FAEnabled() helper
- `app/Http/Controllers/Auth/LoginController.php` - add 2FA redirect logic after successful login
- `routes/web.php` - add 2FA routes

### Implementation Steps

- [ ] 1. Install composer package: `composer require pragmarx/google2fa-laravel`
      Verify: Check composer.json shows the package in require section

- [ ] 2. Create migration for two_factor_auth table with columns: id, user_id (FK unique), secret (encrypted), recovery_codes (json encrypted), enabled_at (nullable), timestamps
      Files: database/migrations/2026_10_31_000001_create_two_factor_auth_table.php
      Verify: Run `php artisan migrate` - table created successfully

- [ ] 3. Create TwoFactorAuth model with fillable fields, casts for array/datetime, encryption for secret/recovery_codes
      Files: app/Models/TwoFactorAuth.php
      Verify: php artisan tinker, App\Models\TwoFactorAuth::count() returns 0

- [ ] 4. Add twoFactorAuth relationship and has2FAEnabled helper to User model
      Files: app/Models/User.php
      Verify: tinker, User::first()->twoFactorAuth returns null

- [ ] 5. Modify LoginController store() method: after Auth::attempt success, check if admin/tutor with 2FA enabled -> redirect to challenge. Admin without 2FA -> force setup.
      Files: app/Http/Controllers/Auth/LoginController.php
      Verify: Login as admin without 2FA, redirected to setup page

- [ ] 6. Create TwoFactorController with showSetup, storeSetup, showChallenge, verifyChallenge, disable methods using Google2FA facade
      Files: app/Http/Controllers/Auth/TwoFactorController.php
      Verify: Visit /2fa/setup route returns 200 response

- [ ] 7. Add 2FA routes to web.php (guest: setup/challenge, auth: disable/recovery)
      Files: routes/web.php
      Verify: `php artisan route:list | grep 2fa` shows all 2FA routes

- [ ] 8. Create Blade views for setup (QR code, recovery codes), challenge (code input), force (admin mandate)
      Files: resources/views/auth/2fa-setup.blade.php, 2fa-challenge.blade.php, 2fa-force.blade.php
      Verify: Visit /2fa/setup, QR code displays correctly

- [ ] 9. Create settings security page with 2FA status, enable/disable buttons, recovery codes section (tutors/admins only)
      Files: resources/views/pages/settings/security.blade.php
      Verify: Visit /settings/security as tutor, 2FA section visible

- [ ] 10. Create TwoFactorEnabledNotification sent on successful 2FA setup
      Files: app/Notifications/TwoFactorEnabledNotification.php
      Verify: Enable 2FA, check email received with confirmation

### Verification Commands

```bash
php artisan migrate
php artisan serve
# Manual browser testing:
# - Register as admin -> forced to /2fa/setup
# - Scan QR with authenticator app -> enter code -> verify login
# - Logout -> login -> verify /2fa/challenge appears
# - Enter 6-digit code -> verify dashboard access
# - Visit /settings/security -> verify 2FA enabled badge
# - Test recovery code flow
# - Disable 2FA -> verify no challenge on next login
```

---

## FEAT-003: Enhanced Password Reset

### Files to Create

- `config/auth.php` (if doesn't exist)
- `database/migrations/2026_10_31_000002_create_password_history_table.php`
- `app/Models/PasswordHistory.php`
- `app/Services/PasswordHistoryService.php`
- `app/Http/Controllers/Auth/ForgotPasswordController.php`
- `app/Http/Controllers/Auth/ResetPasswordController.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`
- `app/Notifications/PasswordResetSuccessNotification.php`
- `app/Http/Requests/UpdatePasswordRequest.php`

### Files to Modify

- `app/Models/User.php` - add passwordHistory() relationship
- `app/Http/Controllers/Auth/RegisterController.php` - store initial password in history
- `routes/web.php` - add password reset routes

### Implementation Steps

- [ ] 1. Create config/auth.php with passwords.users.expire = 15 (minutes), throttle = 60
      Files: config/auth.php
      Verify: `php artisan tinker`, config('auth.passwords.users.expire') returns 15

- [ ] 2. Create migration for password_history table with columns: id, user_id (FK indexed), password (hashed), created_at
      Files: database/migrations/2026_10_31_000002_create_password_history_table.php
      Verify: Run `php artisan migrate` - table created successfully

- [ ] 3. Create PasswordHistory model with fillable fields, no timestamps (created_at only)
      Files: app/Models/PasswordHistory.php
      Verify: tinker, App\Models\PasswordHistory::count() returns 0

- [ ] 4. Add passwordHistory relationship to User model
      Files: app/Models/User.php
      Verify: tinker, User::first()->passwordHistory()->count() returns 0

- [ ] 5. Create PasswordHistoryService with checkPasswordReuse (last 3 check) and storePassword methods
      Files: app/Services/PasswordHistoryService.php
      Verify: tinker, app(App\Services\PasswordHistoryService::class) instantiates

- [ ] 6. Create ForgotPasswordController with rate limiting (3 per hour per email) and sendResetLinkEmail method
      Files: app/Http/Controllers/Auth/ForgotPasswordController.php
      Verify: Visit /forgot-password, submit email 4 times - 4th attempt shows throttle error

- [ ] 7. Create ResetPasswordController with password reuse check and reset method, sends PasswordResetSuccessNotification
      Files: app/Http/Controllers/Auth/ResetPasswordController.php
      Verify: Complete reset flow, notification email received

- [ ] 8. Add password reset routes to web.php (GET/POST forgot, GET/POST reset)
      Files: routes/web.php
      Verify: `php artisan route:list | grep password` shows all routes

- [ ] 9. Create Blade views for forgot-password and reset-password forms
      Files: resources/views/auth/forgot-password.blade.php, reset-password.blade.php
      Verify: Visit /forgot-password, form renders correctly

- [ ] 10. Create PasswordResetSuccessNotification with IP, user agent, timestamp in email body
      Files: app/Notifications/PasswordResetSuccessNotification.php
      Verify: Reset password, check email contains device info

- [ ] 11. Modify RegisterController to store initial password in history after user creation
      Files: app/Http/Controllers/Auth/RegisterController.php (or check OtpController for final user creation)
      Verify: Register new user, check password_history has 1 record

- [ ] 12. Create UpdatePasswordRequest form request for settings password changes with reuse validation
      Files: app/Http/Requests/UpdatePasswordRequest.php
      Verify: Create settings password change route, test reuse validation works

### Verification Commands

```bash
php artisan migrate
php artisan serve
# Manual browser testing:
# - Register new user -> check password_history has 1 entry
# - Visit /forgot-password -> enter email -> receive reset email
# - Click reset link -> attempt same password -> verify error "cannot reuse last 3"
# - Set new password -> verify success email received with device info
# - Request 4 resets within 1 hour -> verify throttle error on 4th
# - Change password 2 more times via settings -> attempt old password -> verify error
# - Request reset token, wait 16 minutes -> use token -> verify "expired" error
```

---

## FEAT-004: Login Activity Tracking

### Files to Create

- `database/migrations/2026_10_31_000003_create_login_activities_table.php`
- `app/Models/LoginActivity.php`
- `app/Services/LoginActivityService.php`
- `app/Http/Controllers/Settings/SecurityController.php`
- `resources/views/pages/settings/security-login-history.blade.php`
- `app/Notifications/SuspiciousLoginNotification.php`
- `app/Helpers/UserAgentHelper.php` (optional)

### Files to Modify

- `app/Models/User.php` - add loginActivities() relationship
- `app/Http/Controllers/Auth/LoginController.php` - call LoginActivityService on success/failure
- `routes/web.php` - add security settings routes

### Implementation Steps

- [ ] 1. Create migration for login_activities table with columns: id, user_id (nullable FK indexed), email (indexed), ip_address, user_agent, status (enum success/failed), reason (nullable), created_at
      Files: database/migrations/2026_10_31_000003_create_login_activities_table.php
      Verify: Run `php artisan migrate` - table created successfully

- [ ] 2. Create LoginActivity model with fillable fields, no timestamps (created_at only), status enum cast
      Files: app/Models/LoginActivity.php
      Verify: tinker, App\Models\LoginActivity::count() returns 0

- [ ] 3. Add loginActivities relationship to User model
      Files: app/Models/User.php
      Verify: tinker, User::first()->loginActivities()->count() returns 0

- [ ] 4. Create LoginActivityService with recordSuccess, recordFailure, checkSuspiciousActivity methods (device fingerprint via user_agent hash, 5 failures in 30 min check)
      Files: app/Services/LoginActivityService.php
      Verify: tinker, app(App\Services\LoginActivityService::class) instantiates

- [ ] 5. Modify LoginController store(): call recordSuccess on Auth::attempt success, recordFailure on failure, check suspicious activity and send notification
      Files: app/Http/Controllers/Auth/LoginController.php
      Verify: Login -> check login_activities has success record

- [ ] 6. Create SuspiciousLoginNotification with email for new device or multiple failures
      Files: app/Notifications/SuspiciousLoginNotification.php
      Verify: Login from different browser -> receive suspicious login email

- [ ] 7. Create Settings/SecurityController with loginHistory method fetching last 10 activities
      Files: app/Http/Controllers/Settings/SecurityController.php
      Verify: Visit /settings/security/login-history route returns 200

- [ ] 8. Add security settings routes to web.php (GET /settings/security, GET /settings/security/login-history)
      Files: routes/web.php
      Verify: `php artisan route:list | grep security` shows routes

- [ ] 9. Create Blade view for login history table with timestamp, status badge, IP, device, reason columns
      Files: resources/views/pages/settings/security-login-history.blade.php
      Verify: Visit /settings/security/login-history, table displays correctly

- [ ] 10. Integrate login history section into existing settings page with last 3 activities and "View all" link
      Files: resources/views/pages/settings/index.blade.php (or security.blade.php)
      Verify: Visit settings page, login history section visible

- [ ] 11. Create UserAgentHelper for parsing user_agent into readable device string (or use jenssegers/agent if available)
      Files: app/Helpers/UserAgentHelper.php
      Verify: tinker, parse user agent string -> returns "Chrome 120 on Windows 10"

### Verification Commands

```bash
php artisan migrate
php artisan serve
# Manual browser testing:
# - Login successfully -> check login_activities has success record
# - Attempt 3 wrong passwords -> check 3 failed records with reason
# - Login from different browser/device -> verify suspicious email sent
# - Make 5 failed attempts within 30 min -> verify suspicious email on 5th
# - Visit /settings/security/login-history -> verify last 10 activities display
# - Test as all user roles (student, tutor, admin, parent) -> all can see their history
```

---

## FEAT-005: Session Management

### Files to Create

- `database/migrations/2026_10_31_000004_create_user_sessions_table.php`
- `app/Models/UserSession.php`
- `app/Http/Middleware/TrackUserSession.php`
- `app/Services/SessionManagementService.php`
- `app/Http/Controllers/Settings/SessionController.php`
- `resources/views/pages/settings/sessions.blade.php`
- `app/Console/Commands/CleanupExpiredSessions.php` (optional)

### Files to Modify

- `app/Models/User.php` - add userSessions() relationship
- `bootstrap/app.php` or `app/Http/Kernel.php` - register TrackUserSession middleware
- `routes/web.php` - add session management routes

### Implementation Steps

- [ ] 1. Verify SESSION_DRIVER=database in .env and sessions table exists (created in 2024_01_01_000001 migration)
      Verify: Check .env, `php artisan tinker`, DB::table('sessions')->count() >= 0

- [ ] 2. Create migration for user_sessions table with columns: id, user_id (FK indexed), session_id (unique), ip_address, user_agent, last_activity, created_at
      Files: database/migrations/2026_10_31_000004_create_user_sessions_table.php
      Verify: Run `php artisan migrate` - table created successfully

- [ ] 3. Create UserSession model with fillable fields, no timestamps (created_at only), scopeActive for filtering by last_activity
      Files: app/Models/UserSession.php
      Verify: tinker, App\Models\UserSession::count() returns 0

- [ ] 4. Add userSessions relationship to User model
      Files: app/Models/User.php
      Verify: tinker, User::first()->userSessions()->count() returns 0

- [ ] 5. Create TrackUserSession middleware: on each auth request, update/create UserSession, check inactivity timeout (2h tutors/admins, 24h students/parents), auto-logout if expired
      Files: app/Http/Middleware/TrackUserSession.php
      Verify: Middleware instantiates without errors

- [ ] 6. Register TrackUserSession in web middleware group in bootstrap/app.php or Kernel.php (after StartSession, Authenticate)
      Files: bootstrap/app.php or app/Http/Kernel.php
      Verify: `php artisan route:list` shows middleware applies to auth routes

- [ ] 7. Create SessionManagementService with getActiveSessions, revokeSession, revokeAllOtherSessions methods (deletes from user_sessions and sessions table)
      Files: app/Services/SessionManagementService.php
      Verify: tinker, app(App\Services\SessionManagementService::class) instantiates

- [ ] 8. Create Settings/SessionController with index (view active sessions), destroy (revoke one), revokeOthers (revoke all except current) methods
      Files: app/Http/Controllers/Settings/SessionController.php
      Verify: Visit /settings/sessions route returns 200

- [ ] 9. Add session management routes to web.php (GET /settings/sessions, DELETE /settings/sessions/{session}, POST /settings/sessions/revoke-others)
      Files: routes/web.php
      Verify: `php artisan route:list | grep sessions` shows routes

- [ ] 10. Create Blade view for sessions table with IP, device, last active, current badge, revoke buttons, "Sign out all other devices" button with password confirm
      Files: resources/views/pages/settings/sessions.blade.php
      Verify: Visit /settings/sessions, sessions list displays correctly

- [ ] 11. Integrate sessions section into existing settings page sidebar/tabs, link from security overview
      Files: resources/views/pages/settings/index.blade.php (or create settings layout)
      Verify: Settings navigation includes sessions link

- [ ] 12. (Optional) Create CleanupExpiredSessions command for daily cleanup of old user_sessions records
      Files: app/Console/Commands/CleanupExpiredSessions.php, app/Console/Kernel.php
      Verify: `php artisan list | grep cleanup` shows command

### Verification Commands

```bash
php artisan migrate
php artisan serve
# Manual browser testing:
# - Login as tutor in browser A -> check user_sessions has 1 record
# - Login as same tutor in incognito browser B -> check user_sessions has 2 records
# - Visit /settings/sessions -> verify both sessions visible, one marked "Current"
# - Click revoke on non-current session -> verify browser B is logged out
# - Login in 3 browsers -> click "Sign out all other devices" -> verify only current remains
# - Login as tutor -> wait 2h 1min -> make request -> verify auto-logout with "Session expired"
# - Login as student -> wait 2h 1min -> make request -> verify still logged in
# - Login as student -> wait 24h 1min -> make request -> verify auto-logout
```

---

## Composer Packages to Install

1. **pragmarx/google2fa-laravel** (for FEAT-002 2FA) - includes google2fa and bacon-qr-code
   ```bash
   composer require pragmarx/google2fa-laravel
   ```

No other external packages are required. All other features use Laravel built-in functionality.

---

## Database Migrations Summary

4 new tables to create:

1. `two_factor_auth` - stores 2FA secrets and recovery codes per user
2. `password_history` - stores last N password hashes per user
3. `login_activities` - logs all login attempts with status and device info
4. `user_sessions` - tracks active sessions with last activity timestamp

All use foreign keys with cascade delete on user deletion.

---

## Routes Summary

All routes added to `routes/web.php`:

### 2FA Routes (FEAT-002)
- GET/POST /2fa/setup (guest or auth with check)
- GET/POST /2fa/challenge (guest with session check)
- POST /2fa/disable (auth)
- POST /2fa/recovery (auth - regenerate recovery codes)

### Password Reset Routes (FEAT-003)
- GET /forgot-password
- POST /forgot-password (named password.email)
- GET /reset-password/{token} (named password.reset)
- POST /reset-password (named password.update)

### Security Settings Routes (FEAT-004)
- GET /settings/security (auth - overview)
- GET /settings/security/login-history (auth - full activity table)

### Session Management Routes (FEAT-005)
- GET /settings/sessions (auth)
- DELETE /settings/sessions/{session} (auth)
- POST /settings/sessions/revoke-others (auth)

---

## UI Integration Points

1. **Dashboard/Settings Navigation** - add links to:
   - /settings/security (security overview)
   - /settings/security/login-history (activity log)
   - /settings/sessions (active sessions)

2. **Admin/Tutor Settings** - show 2FA section with:
   - Status badge (enabled/disabled)
   - Enable/Disable buttons
   - Recovery codes view/regenerate

3. **Login Flow** - add conditional redirects:
   - Successful password login → check 2FA → /2fa/challenge or /dashboard
   - Admin first login → /2fa/setup (forced)

4. **Email Notifications** - 4 new emails:
   - TwoFactorEnabledNotification (2FA setup confirmation)
   - PasswordResetSuccessNotification (password changed alert)
   - SuspiciousLoginNotification (new device or multiple failures)

---

## Verification Checklist

After implementing all features, verify:

- [ ] All 4 migrations run successfully without errors
- [ ] Composer package pragmarx/google2fa-laravel installed
- [ ] Admin forced to setup 2FA on first login
- [ ] Tutor can optionally enable 2FA from settings
- [ ] Students/parents never see 2FA prompts
- [ ] Password reset tokens expire after 15 minutes
- [ ] Cannot reuse last 3 passwords in any password change flow
- [ ] Password reset rate limited to 3 per hour per email
- [ ] Every login attempt creates a login_activities record
- [ ] Suspicious login email sent on new device or 5 failures
- [ ] Login history shows last 10 activities in settings
- [ ] Every auth request creates/updates user_sessions record
- [ ] Tutors/admins auto-logout after 2 hours inactivity
- [ ] Students/parents auto-logout after 24 hours inactivity
- [ ] Active sessions list shows all current sessions
- [ ] Can revoke individual sessions or all others
- [ ] All features work for all user roles (student, parent, tutor, admin)
- [ ] No errors in storage/logs/laravel.log during any flow

---

## Notes

- **No Tests:** The project has no test framework installed. All verification is manual via browser testing and log checking.
- **Migration Guards:** Use `Schema::hasTable('password_reset_tokens')` guards since that table already exists from a prior migration.
- **Session Driver:** Verify .env has SESSION_DRIVER=database (required for FEAT-005).
- **CSS Styling:** Match existing Blade view patterns with inline CSS using custom properties (--ink, --cream, --trust-blue, etc.) and DM Sans font.
- **Email Sending:** Ensure .env has correct MAIL_* configuration for notifications to send successfully.

---

**Implementation Order:** Follow feature order (FEAT-002 → FEAT-003 → FEAT-004 → FEAT-005) to maintain logical dependency flow, though features are independent.
