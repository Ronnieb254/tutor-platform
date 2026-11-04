# Implementation Plan: Font Awesome & SweetAlert2 Integration

## Context

Font Awesome 6 and SweetAlert2 are **already loaded** in all main layouts:
- `resources/views/layouts/app.blade.php` - Lines 13-14
- `resources/views/layouts/guest.blade.php` - Line 15
- `resources/views/layouts/admin.blade.php` - Line 13  
- `resources/views/pages/home.blade.php` - Line 14

The `public/js/sweetalert-config.js` helper file exists with these functions:
- `showSuccess(title, message)` - Success modal
- `showError(title, message)` - Error modal
- `showConfirm(title, message, confirmText, cancelText)` - Confirmation dialog
- `showToast(message, icon)` - Toast notification
- `showLoading(message)` / `hideLoading()` - Loading states
- `confirmAction(url, method, title, message)` - Confirm + AJAX action
- `cancelBooking(cancelUrl)` - Booking cancellation with reason

**This is a cleanup task**: standardize inconsistent icon and alert patterns across the codebase.

---

## Part 1: Replace Emoji Icons with Font Awesome

### Emoji Usage Found

**Dashboard pages:**
- `tutor.blade.php` - Line 356: ✅ profile live banner, Lines 823-826: 📅✅❌💰 notification icons
- `parent.blade.php` - Line 528: 📅✅❌ notification icons
- `partials/documents.blade.php` - Line 39: ✅ verification status

**Library/Resources:**
- `tutor/library/index.blade.php` - Lines 109, 205: 📚 library icon
- `tutor/library/create.blade.php` - Line 207: ✅ file selected
- `library/index.blade.php` - Lines 102, 219: 📚 resource hub
- `courses/index.blade.php` - Line 43: 📐🔬📚✏️🎯💡 subject placeholders
- `blog/index.blade.php` - Line 51: 📐🔬📚✏️🎯📝 post placeholders

**Auth/Onboarding:**
- `auth/tutor-onboarding.blade.php` - Lines 241, 267, 293, 319, 345: ✅ upload success

**Admin:**
- `admin/library.blade.php` - Line 7: 📚 page title, Line 301: emoji icon field
- `admin/subjects.blade.php` - Line 18: ✅ success message

**Other:**
- `clinic-payment-pending.blade.php` - Line 63: ✅ payment confirmed
- `emails/booking-confirmed-student.blade.php` - Line 29: ✅ (email - may keep for compatibility)
- `components/security-settings.blade.php` - Line 14: ✓ success

### Icon Mapping

| Emoji | Font Awesome Class | Semantic Color |
|-------|-------------------|----------------|
| ✅ / ✓ | `fa-solid fa-check-circle` | `#10B981` (--mint) |
| 📚 | `fa-solid fa-book` | `#0A64BC` (--trust-blue) |
| 👤 | `fa-solid fa-user` | Current color |
| 📅 | `fa-solid fa-calendar` | `#0A64BC` |
| ❌ | `fa-solid fa-times-circle` | `#E8522A` (--coral) |
| 💰 | `fa-solid fa-money-bill-wave` | `#EEBE15` (--gold) |
| 🔔 | `fa-solid fa-bell` | `#0A64BC` |
| 📐 | `fa-solid fa-ruler` | Varies by context |
| 🔬 | `fa-solid fa-flask` | Varies |
| ✏️ | `fa-solid fa-pencil` | Varies |
| 🎯 | `fa-solid fa-bullseye` | Varies |
| 💡 | `fa-solid fa-lightbulb` | Varies |
| 📝 | `fa-solid fa-file-lines` | Varies |

### Implementation Steps

- [ ] 1. **Replace dashboard notification icons**
      Files: 
      - `resources/views/pages/dashboard/tutor.blade.php` (lines 823-826)
      - `resources/views/pages/dashboard/parent.blade.php` (line 528)
      
      Replace emoji arrays with FA icon class arrays:
      ```php
      // Before: ['📅','rgba(10,100,188,.1)']
      // After: ['<i class="fas fa-calendar"></i>','rgba(10,100,188,.1)']
      ```
      
      Verify: Visit `/dashboard` as tutor and parent, check notifications panel shows FA icons

- [ ] 2. **Replace status indicator emojis**
      Files:
      - `resources/views/pages/dashboard/tutor.blade.php` (line 356)
      - `resources/views/pages/dashboard/partials/documents.blade.php` (line 39)
      - `resources/views/pages/clinic-payment-pending.blade.php` (line 63)
      
      Replace with:
      ```html
      <i class="fas fa-check-circle" style="color:#10B981;font-size:24px;"></i>
      ```
      
      Verify: Visit tutor dashboard and document verification page, check green check icons appear

- [ ] 3. **Replace library/resource icons**
      Files:
      - `resources/views/pages/tutor/library/index.blade.php` (lines 109, 205)
      - `resources/views/pages/library/index.blade.php` (lines 102, 219)
      - `resources/views/pages/admin/library.blade.php` (line 7)
      
      Replace 📚 with:
      ```html
      <i class="fas fa-book"></i>
      ```
      
      Verify: Visit `/tutor/library` and `/library`, check book icons display

- [ ] 4. **Replace upload success indicators**
      Files:
      - `resources/views/pages/auth/tutor-onboarding.blade.php` (lines 241, 267, 293, 319, 345)
      - `resources/views/pages/tutor/library/create.blade.php` (line 207)
      
      Replace ✅ with:
      ```html
      <i class="fas fa-check-circle" style="color:#10B981;"></i>
      ```
      
      Verify: Visit tutor onboarding, upload a file, check FA check icon appears

- [ ] 5. **Replace course/blog placeholder icons**
      Files:
      - `resources/views/pages/courses/index.blade.php` (line 43)
      - `resources/views/pages/blog/index.blade.php` (line 51)
      
      Replace emoji arrays with FA arrays:
      ```php
      // Before: ['📐','🔬','📚','✏️','🎯','💡']
      // After: ['<i class="fas fa-ruler"></i>','<i class="fas fa-flask"></i>','<i class="fas fa-book"></i>','<i class="fas fa-pencil"></i>','<i class="fas fa-bullseye"></i>','<i class="fas fa-lightbulb"></i>']
      ```
      
      Verify: Visit `/courses` and check placeholder icons are Font Awesome, not emojis

- [ ] 6. **Replace admin panel icons**
      Files:
      - `resources/views/pages/admin/subjects.blade.php` (line 18)
      - `resources/views/components/security-settings.blade.php` (line 14)
      
      Replace success check emojis with FA icons
      
      Verify: Visit admin subjects page and security settings, check success messages use FA icons

- [ ] 7. **Final icon verification**
      Run: `php artisan optimize:clear`
      Then: Manually browse all major pages and verify no emojis remain, all icons are FA
      
      Verify: Run `grep -r '✅\|📚\|👤\|📅\|❌\|💰\|🔔' resources/views --include='*.blade.php'` - should return zero results (or only in email templates if preserved for email client compatibility)

---

## Part 2: Replace Native Alerts with SweetAlert2

### Native Alert/Confirm Usage Found

**Auth pages:**
- `auth/verify-otp.blade.php` - Lines 394, 400: `alert()` for resend errors
- `auth/register.blade.php` - Line 627: `alert()` for registration failure
- `auth/login.blade.php` - Line 532: `alert()` for login failure
- `auth/2fa-setup.blade.php` - Lines 313, 315: `alert()` for clipboard operations
- `auth/2fa-force.blade.php` - Lines 351, 359: `alert()` for recovery code copy

**Dashboard/Bookings:**
- `pages/bookings/show.blade.php` - Line 483: `confirm()` for booking cancellation
- `pages/bookings/meeting-room.blade.php` - Line 214: `alert()` for copy failure
- `pages/clinic-bbb-room.blade.php` - Line 196: `alert()` for copy failure
- `pages/dashboard/admin.blade.php` - Lines 247, 280, 286: `confirm()` and `alert()` for booking approval
- `pages/dashboard/parent.blade.php` - Lines 290, 351: `confirm()` for cancellation and refund
- `pages/dashboard/student.blade.php` - Line 425: `confirm()` for cancellation

**Forms/Validation:**
- `pages/auth/tutor-onboarding.blade.php` - Lines 542, 552, 623, 628: `alert()` for file size and submission errors
- `pages/tutors/show.blade.php` - Lines 743, 747, 793, 814, 824, 830: `alert()` for payment validation and errors
- `components/security-settings.blade.php` - Lines 69, 80, 113, 115, 125, 127: `confirm()` and `alert()` for 2FA actions

### Implementation Steps

- [ ] 1. **Replace auth page alerts**
      Files:
      - `resources/views/auth/verify-otp.blade.php`
      - `resources/views/auth/register.blade.php`
      - `resources/views/auth/login.blade.php`
      
      Replace `alert(message)` with `showError('Error', message)` or `showToast(message, 'error')`
      
      Verify: Trigger validation errors on login/register forms, check SweetAlert2 error appears

- [ ] 2. **Replace 2FA and clipboard alerts**
      Files:
      - `resources/views/auth/2fa-setup.blade.php`
      - `resources/views/auth/2fa-force.blade.php`
      - `resources/views/components/security-settings.blade.php`
      
      Replace `alert()` with `showToast()` for clipboard operations
      Replace `confirm()` with `showConfirm()` for 2FA disable/regenerate
      
      Example:
      ```javascript
      // Before: alert('Recovery codes copied!');
      // After: showToast('Recovery codes copied!', 'success');
      
      // Before: onclick="return confirm('Disable 2FA?')"
      // After: onclick="showConfirm('Disable Two-Factor Authentication', 'Are you sure?', 'Yes, Disable', 'Cancel').then((result) => { if (result.isConfirmed) this.closest('form').submit(); })"
      ```
      
      Verify: Enable 2FA, copy recovery codes, disable 2FA - all should show SweetAlert2 dialogs

- [ ] 3. **Replace booking cancellation confirms**
      Files:
      - `resources/views/pages/bookings/show.blade.php`
      - `resources/views/pages/dashboard/parent.blade.php`
      - `resources/views/pages/dashboard/student.blade.php`
      
      Replace inline `onsubmit="return confirm()"` with Alpine.js or JS event listener using `showConfirm()`
      
      Example:
      ```html
      <!-- Before -->
      <form onsubmit="return confirm('Cancel this booking?')">
      
      <!-- After -->
      <form @submit.prevent="handleCancel">
      <script>
      function handleCancel(e) {
          showConfirm('Cancel Booking', 'This action cannot be undone.', 'Yes, Cancel', 'No').then((result) => {
              if (result.isConfirmed) e.target.submit();
          });
      }
      </script>
      ```
      
      Verify: Navigate to a booking, click Cancel, verify SweetAlert2 confirmation dialog appears with correct styling

- [ ] 4. **Replace admin booking confirmation**
      Files:
      - `resources/views/pages/dashboard/admin.blade.php`
      
      Replace `confirm()` in `confirmBooking()` function with `showConfirm()`
      Keep loading state logic intact
      
      Verify: Login as admin, confirm a pending booking, check SweetAlert2 confirm dialog appears

- [ ] 5. **Replace tutor onboarding alerts**
      Files:
      - `resources/views/pages/auth/tutor-onboarding.blade.php`
      
      Replace file size and submission error `alert()` calls with `showError()`
      
      Example:
      ```javascript
      // Before: alert('File too large. Maximum 5MB allowed.');
      // After: showError('File Too Large', 'Maximum file size is 5MB. Please choose a smaller file.');
      ```
      
      Verify: Upload oversized file during tutor onboarding, check SweetAlert2 error modal

- [ ] 6. **Replace payment flow alerts**
      Files:
      - `resources/views/pages/tutors/show.blade.php`
      
      Replace validation and payment error `alert()` calls with `showError()`
      Keep polling and STK push logic intact
      
      Verify: Book a session with invalid data, check SweetAlert2 errors appear

- [ ] 7. **Replace clipboard operation alerts**
      Files:
      - `resources/views/pages/bookings/meeting-room.blade.php`
      - `resources/views/pages/clinic-bbb-room.blade.php`
      
      Replace copy failure `alert()` with `showToast(message, 'error')`
      
      Verify: In meeting room, copy session code, check toast notification

- [ ] 8. **Final alert verification**
      Run: `php artisan optimize:clear`
      Then: Test all interactive flows manually
      
      Test checklist:
      - [ ] Login with wrong credentials → SweetAlert2 error
      - [ ] Register with invalid data → SweetAlert2 error  
      - [ ] Cancel a booking → SweetAlert2 confirm dialog
      - [ ] Upload oversized file in tutor onboarding → SweetAlert2 error
      - [ ] Copy recovery codes in 2FA setup → SweetAlert2 toast
      - [ ] Confirm booking as admin → SweetAlert2 confirm
      - [ ] Book session with invalid phone → SweetAlert2 error
      
      Verify: Run `grep -rn 'alert(' resources/views --include='*.blade.php' | grep -v 'showAlert\|SweetAlert\|sweetalert'` - should return zero results

---

## Final Verification

After completing both parts:

1. **Clear all caches:**
   ```bash
   php artisan optimize:clear
   ```

2. **Start development server:**
   ```bash
   php artisan serve
   ```

3. **Manual browser testing:**
   - Visit home page, dashboard (all roles), library, courses, admin panel
   - Check no emoji icons visible - all should be Font Awesome
   - Trigger various alerts/confirms - all should be SweetAlert2 styled
   - Check browser console for any JS errors

4. **Grep verification:**
   ```bash
   # Check for remaining emojis (should be zero or only in email templates)
   grep -r '✅\|📚\|👤\|📅\|❌\|💰\|🔔' resources/views --include='*.blade.php'
   
   # Check for native alerts (should be zero)
   grep -rn 'alert(' resources/views --include='*.blade.php' | grep -v 'showAlert\|SweetAlert\|sweetalert'
   
   # Check for native confirms (should be zero or wrapped in SweetAlert)
   grep -rn 'confirm(' resources/views --include='*.blade.php' | grep -v 'showConfirm\|Confirm'
   ```

5. **Functionality check:**
   - All existing features work identically
   - Better user experience with consistent, styled notifications
   - No regressions in form submissions, booking flows, or admin actions

---

## Notes

- **Email templates**: Consider keeping emojis in `emails/booking-confirmed-student.blade.php` for email client compatibility, as Font Awesome requires CSS which may not render in all email clients
- **Color scheme**: Preserve existing CSS variables: `--mint` (success), `--coral` (error), `--trust-blue` (info), `--gold` (warning)
- **Inline styles**: Many blade files use inline styles - preserve these when adding FA icons
- **AlpineJS**: Some forms use Alpine.js for reactivity - ensure SweetAlert2 integrates properly with Alpine directives
- **Loading states**: Preserve existing loading state logic in payment and booking flows when replacing alerts
