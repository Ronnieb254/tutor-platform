# Implementation Plan: Add Group Session Type

## Overview
Add "Group Session" as a fourth session type option throughout the Mwalimu tutoring platform. Group sessions are 60 minutes and cost KES 500 (fixed), creating a conflict with the existing 60-minute "Deep Dive" individual session that costs KES 700. To differentiate them, we'll add a `session_type` column to the bookings table.

**Project Type**: Laravel 11 / Livewire 3 web application
**Database**: MySQL
**Build**: No build step required (Laravel serves PHP directly)
**Test**: Manual testing via `php artisan serve` and browser verification

---

## Implementation Steps

- [ ] 1. **Create database migration to add session_type column to bookings table**
      
      Create a new migration file that adds `session_type` ENUM column with values 'individual' and 'group', defaulting to 'individual' for existing rows.
      
      **Files**: 
      - `database/migrations/YYYY_MM_DD_HHMMSS_add_session_type_to_bookings.php` (new file)
      
      **Changes**:
      ```php
      Schema::table('bookings', function (Blueprint $table) {
          $table->enum('session_type', ['individual', 'group'])
                ->default('individual')
                ->after('duration_minutes');
      });
      ```
      
      **Verify**: Run `php artisan migrate` and confirm no errors. Check the bookings table structure with `php artisan tinker` then `DB::select('DESCRIBE bookings');` to verify the new column exists.

- [ ] 2. **Update Booking model fillable array**
      
      Add 'session_type' to the $fillable array in the Booking model so it can be mass-assigned.
      
      **Files**: 
      - `app/Models/Booking.php`
      
      **Changes**: 
      In the `$fillable` array (line 13), add `'session_type'` after `'duration_minutes'`.
      
      **Verify**: Run `php artisan tinker`, then execute:
      ```php
      $booking = new App\Models\Booking();
      in_array('session_type', $booking->getFillable()); // Should return true
      ```

- [ ] 3. **Update TutorProfile::sessionPrices() to include group session**
      
      Modify the sessionPrices() method to return group session data. Because PHP arrays can't have duplicate keys (both individual 60 and group 60), we'll use a nested structure with session_type as part of the key strategy. Change the array to use composite string keys like "60_individual" and "60_group".
      
      **Files**: 
      - `app/Models/TutorProfile.php`
      
      **Changes**:
      Replace the sessionPrices() method (currently around line 62-67):
      ```php
      public static function sessionPrices(): array
      {
          return [
              '20_individual' => ['name' => 'Quick Help',       'price' => 300, 'icon' => '⚡', 'desc' => 'Rapid revision or a quick question', 'duration' => 20, 'type' => 'individual'],
              '40_individual' => ['name' => 'Guided Practice',  'price' => 500, 'icon' => '🎯', 'desc' => 'Full structured lesson with Q&A', 'duration' => 40, 'type' => 'individual'],
              '60_individual' => ['name' => 'Deep Dive',        'price' => 700, 'icon' => '🏆', 'desc' => 'Maximum learning, best value', 'duration' => 60, 'type' => 'individual'],
              '60_group'      => ['name' => 'Group Session',    'price' => 500, 'icon' => '👥', 'desc' => 'Learn together, share the cost', 'duration' => 60, 'type' => 'group'],
          ];
      }
      ```
      
      **Verify**: Run `php artisan tinker`, then:
      ```php
      $prices = App\Models\TutorProfile::sessionPrices();
      isset($prices['60_group']); // Should return true
      $prices['60_group']['price'] === 500; // Should return true
      ```

- [ ] 4. **Add TutorProfile::getPriceForSession() helper method**
      
      Create a new method that accepts both duration and session_type to return the correct price. Keep the existing getPriceForDuration() for backward compatibility but update it to default to 'individual'.
      
      **Files**: 
      - `app/Models/TutorProfile.php`
      
      **Changes**: 
      Add this new method after getPriceForDuration() (around line 90):
      ```php
      /**
       * Get the price for a specific session duration and type.
       */
      public function getPriceForSession(int $minutes, string $type = 'individual'): float
      {
          $key = "{$minutes}_{$type}";
          return self::sessionPrices()[$key]['price'] ?? 300;
      }
      
      /**
       * Get the session name for a duration and type.
       */
      public function getSessionNameForSession(int $minutes, string $type = 'individual'): string
      {
          $key = "{$minutes}_{$type}";
          return self::sessionPrices()[$key]['name'] ?? 'Session';
      }
      ```
      
      Update existing getPriceForDuration() to use the new helper:
      ```php
      public function getPriceForDuration(int $minutes): float
      {
          return $this->getPriceForSession($minutes, 'individual');
      }
      ```
      
      **Verify**: Run `php artisan tinker`, then:
      ```php
      $tp = App\Models\TutorProfile::first();
      $tp->getPriceForSession(60, 'group'); // Should return 500.0
      $tp->getPriceForSession(60, 'individual'); // Should return 700.0
      ```

- [ ] 5. **Update BookingController store() validation**
      
      Add validation for the new session_type field and update duration validation to include 20 (currently it's 15,30,45,60).
      
      **Files**: 
      - `app/Http/Controllers/BookingController.php`
      
      **Changes**: 
      In the store() method validation (around line 41-48), update to:
      ```php
      $request->validate([
          'tutor_profile_id' => 'required|exists:tutor_profiles,id',
          'subject_id'       => 'nullable|exists:subjects,id',
          'scheduled_at'     => 'required|date|after:now',
          'duration_minutes' => 'required|in:20,40,60',
          'session_type'     => 'required|in:individual,group',
          'booking_for'      => 'nullable|in:self,child',
          'child_profile_id' => 'nullable|exists:child_profiles,id',
          'notes'            => 'nullable|string|max:500',
      ]);
      ```
      
      **Verify**: Start the dev server with `php artisan serve`, then use a tool like Postman or browser dev tools to submit a booking request and confirm validation accepts 'session_type' => 'group'.

- [ ] 6. **Update BookingService createBooking() to handle session_type**
      
      Modify the createBooking() method to accept and store session_type, and use getPriceForSession() to calculate the correct price.
      
      **Files**: 
      - `app/Services/BookingService.php`
      
      **Changes**: 
      In createBooking() method (around line 31), update the price calculation:
      ```php
      public function createBooking(array $data): Booking
      {
          $studentId = $data['student_id'];
          $tutorProfile = TutorProfile::findOrFail($data['tutor_profile_id']);
          $minutes  = $data['duration_minutes'];
          $sessionType = $data['session_type'] ?? 'individual';
          
          // No free sessions for group type
          $isFree   = ($sessionType === 'individual' && $minutes === 20) 
                      && TutorProfile::isFreeFirstSession($studentId, $tutorProfile->user_id);
          $amount   = $isFree ? 0.00 : ($tutorProfile->getPriceForSession($minutes, $sessionType));
          $fees     = $this->calculateFees($amount);
          
          return DB::transaction(function () use ($data, $fees, $tutorProfile, $sessionType) {
              $uuid = (string) Str::uuid();
              
              $booking = Booking::create([
                  'uuid'             => $uuid,
                  'student_id'       => $data['student_id'],
                  'tutor_id'         => $tutorProfile->user_id,
                  'subject_id'       => $data['subject_id'] ?? null,
                  'scheduled_at'     => $data['scheduled_at'],
                  'duration_minutes' => $data['duration_minutes'],
                  'session_type'     => $sessionType,
                  // ... rest of the fields remain the same
              ]);
              
              return $booking;
          });
      }
      ```
      
      **Verify**: Create a test booking via tinker:
      ```php
      $service = app(App\Services\BookingService::class);
      $booking = $service->createBooking([
          'student_id' => 1,
          'tutor_profile_id' => 1,
          'scheduled_at' => now()->addDay(),
          'duration_minutes' => 60,
          'session_type' => 'group',
      ]);
      $booking->session_type === 'group' && $booking->total_amount == 500;
      ```

- [ ] 7. **Update BookingWizard Livewire component**
      
      Add a session_type property and update the component to allow students to choose between individual and group sessions when duration is 60 minutes.
      
      **Files**: 
      - `app/Livewire/Booking/BookingWizard.php`
      - `resources/views/livewire/booking/booking-wizard.blade.php`
      
      **Changes in PHP class**: 
      Add property (around line 17):
      ```php
      public string $sessionType = 'individual';
      ```
      
      Update validation in nextStep() (around line 54):
      ```php
      $this->validate([
          'duration'      => 'required|in:20,40,60',
          'sessionType'   => 'required|in:individual,group',
          'scheduledDate' => 'required|date|after:today',
          'scheduledTime' => 'required',
      ]);
      ```
      
      Update initPayment() to pass session_type (around line 74):
      ```php
      $booking = $bookingService->createBooking([
          'tutor_profile_id' => $this->tutorProfile->id,
          'student_id'       => auth()->id(),
          'subject_id'       => $this->subjectId,
          'scheduled_at'     => $scheduledAt,
          'duration_minutes' => $this->duration,
          'session_type'     => $this->sessionType,
          'booking_for'      => $this->bookingFor,
          'child_profile_id' => $this->childProfileId,
          'notes'            => $this->notes,
      ]);
      ```
      
      **Changes in Blade view**: 
      Find the duration selection UI (search for "duration" radio buttons or cards) and add session type selection. Add radio buttons or toggle for session_type when duration is 60.
      
      **Verify**: Visit a tutor profile page, click "Book Session", select 60 minutes, toggle between individual and group, and verify the price updates correctly.

- [ ] 8. **Update tutor-search.blade.php duration blocks**
      
      Replace the hardcoded 15/30/45/60 blocks with 20/40/60-individual and 60-group, showing all four session types as cards.
      
      **Files**: 
      - `resources/views/livewire/tutors/tutor-search.blade.php`
      
      **Changes**: 
      Find the duration blocks grid (around line 101, currently `@foreach([15, 30, 45, 60] as $mins)`), replace with:
      ```blade
      <div class="grid grid-cols-4 gap-1.5 mt-4">
          @foreach(['20_individual', '40_individual', '60_individual', '60_group'] as $key)
          @php
              $session = \App\Models\TutorProfile::sessionPrices()[$key];
          @endphp
          <div class="text-center rounded-xl border border-hairline py-1.5 hover:border-trust-blue hover:bg-trust-blue/5 cursor-pointer transition"
               title="{{ $session['desc'] }}">
              <p class="text-[11px] font-bold text-ink">{{ $session['icon'] }} {{ $session['duration'] }}m</p>
              <p class="text-[10px] text-muted">KSh {{ $session['price'] }}</p>
          </div>
          @endforeach
      </div>
      ```
      
      **Verify**: Visit `/tutors` and confirm all tutor cards show 4 session type blocks with correct icons and prices.

- [ ] 9. **Update tutor-onboarding.blade.php pricing preview**
      
      Update the "Your session prices" preview in Step 1 to show all 4 session types including the new group session.
      
      **Files**: 
      - `resources/views/livewire/tutor/tutor-onboarding.blade.php`
      
      **Changes**: 
      Find the session pricing preview section (around line 42, currently `@foreach([15 => 0.25, 30 => 0.50, 45 => 0.75, 60 => 1.00] as $mins => $mult)`), replace with:
      ```blade
      <div class="rounded-2xl bg-trust-blue/5 border border-trust-blue/15 p-4">
          <p class="text-sm font-semibold text-trust-blue mb-3">Platform session prices (fixed for all tutors)</p>
          <div class="grid grid-cols-4 gap-2">
              @foreach(\App\Models\TutorProfile::sessionPrices() as $key => $session)
              <div class="text-center rounded-xl bg-white border border-hairline p-2">
                  <p class="text-xs font-bold text-ink">{{ $session['icon'] }} {{ $session['duration'] }}min</p>
                  <p class="text-[10px] text-muted">{{ $session['name'] }}</p>
                  <p class="text-xs text-trust-blue font-semibold mt-0.5">KSh {{ number_format($session['price'], 0) }}</p>
              </div>
              @endforeach
          </div>
      </div>
      ```
      
      **Verify**: Visit `/tutor/onboarding` (while logged in as a new tutor), go to Step 1, and verify the pricing preview shows all 4 session types.

- [ ] 10. **Update home.blade.php pricing section**
       
       Update the homepage pricing cards/references to include the group session option.
       
       **Files**: 
       - `resources/views/pages/home.blade.php`
       
       **Changes**: 
       Find the $prices and $sessionNames arrays at the top (around line 22-23). Replace with:
       ```php
       $sessionPrices = \App\Models\TutorProfile::sessionPrices();
       ```
       
       Then find any foreach loops iterating over prices and update to use $sessionPrices. For example, if there's a pricing cards section, ensure it includes all 4 types.
       
       **Verify**: Visit `/` (homepage) and confirm the pricing section displays all 4 session types with correct names, icons, and prices.

- [ ] 11. **Update how-it-works.blade.php pricing references**
       
       Update the "Pick Session Type & Time" section to reflect 4 session types instead of 3.
       
       **Files**: 
       - `resources/views/pages/how-it-works.blade.php`
       
       **Changes**: 
       Find the student steps array (around line 40-45), specifically the step describing session types. Replace the hardcoded bullet points with:
       ```blade
       ['2','fa-clock','#10B981','Pick Session Type & Time','Choose your session type — Quick Help (20min), Guided Practice (40min), Deep Dive (60min individual), or Group Session (60min group). Fixed pricing, no surprises.',
           'Quick Help – 20 min – KSh 300',
           'Guided Practice – 40 min – KSh 500',
           'Deep Dive – 60 min – KSh 700',
           'Group Session (👥) – 60 min – KSh 500',
           'Same price for every tutor'],
       ```
       
       **Verify**: Visit `/how-it-works` and confirm the "Pick Session Type & Time" step lists all 4 options.

- [ ] 12. **Update bookings/show.blade.php to display session type**
       
       Add session_type display in the booking detail view so users can see whether it's an individual or group session.
       
       **Files**: 
       - `resources/views/pages/bookings/show.blade.php`
       
       **Changes**: 
       Find the session metadata section (around line 34, where duration and subject are displayed). Add session type badge:
       ```blade
       <span>
           <i class="fa-solid fa-{{ $booking->session_type === 'group' ? 'users' : 'user' }} fa-xs" 
              style="color:#0A64BC;margin-right:5px;"></i>
           {{ ucfirst($booking->session_type) }} Session
       </span>
       ```
       
       Update the session name retrieval (around line 28):
       ```php
       $sessionName = $tutorProfile->getSessionNameForSession($booking->duration_minutes, $booking->session_type) ?? 'Session';
       ```
       
       **Verify**: Create a group booking, then visit `/bookings/{uuid}` and confirm the session type is displayed correctly.

- [ ] 13. **Update tutor-onboarding.blade.php static page**
       
       Update the static tutor onboarding info page to mention the 4 session types.
       
       **Files**: 
       - `resources/views/pages/auth/tutor-onboarding.blade.php`
       
       **Changes**: 
       Find any text mentioning "3 session types" or listing the session types and update to include Group Session. If there's an info box listing session types, add the 4th one.
       
       **Verify**: Visit the static onboarding page and confirm text reflects 4 session types.

- [ ] 14. **Update footer.blade.php if it mentions pricing**
       
       Check the footer partial for any hardcoded pricing mentions and update if needed.
       
       **Files**: 
       - `resources/views/partials/footer.blade.php`
       
       **Changes**: 
       Review the file for any mentions of session durations or prices (search for "300", "500", "700", "15", "30", "45", "60"). Update any static text to reflect the new 4-option structure. Based on the file read, the footer doesn't appear to have hardcoded pricing, so this may be a no-op.
       
       **Verify**: View the footer on any page and confirm no outdated pricing information is displayed.

- [ ] 15. **Update email notification templates if they mention session details**
       
       Check if BookingConfirmedTutor and BookingConfirmedStudent reference session names or prices, and ensure they use the new helper methods.
       
       **Files**: 
       - `app/Notifications/BookingConfirmedTutor.php`
       - `app/Notifications/BookingConfirmedStudent.php`
       - `resources/views/emails/booking-confirmed-tutor.blade.php` (if exists)
       - `resources/views/emails/booking-confirmed-student.blade.php` (if exists)
       
       **Changes**: 
       The notification classes use `->view('emails.booking-confirmed-tutor', ['booking' => $this->booking])` but the email view files were not found during exploration. Search for these files:
       ```bash
       find resources/views/emails -name "booking-confirmed*"
       ```
       
       If they exist and display session info, update them to show session_type. If they call $booking->formattedDuration() or similar, ensure the display includes the session type context.
       
       **Verify**: Create a test booking and check the email sent (check logs with `tail -f storage/logs/laravel.log` or use Mailtrap/MailHog). Confirm session type is mentioned if the email displays session details.

- [ ] 16. **Search for any other hardcoded session duration arrays**
       
       Grep the codebase for remaining instances of hardcoded duration arrays like `[15,30,45,60]` or `[15 => ..., 30 => ...]` and update them to use TutorProfile::sessionPrices().
       
       **Files**: 
       - Run: `grep -r "15.*30.*45.*60" app/ resources/views/ --include="*.php" --include="*.blade.php"`
       - Update any found instances
       
       **Changes**: 
       Replace hardcoded arrays with dynamic calls to TutorProfile::sessionPrices() where appropriate. Some validation arrays may remain hardcoded (like in BookingController), which is fine as long as they're updated to [20,40,60].
       
       **Verify**: Run the grep command again after changes and confirm no outdated hardcoded duration lists remain.

- [ ] 17. **Update any JavaScript/Alpine.js code in blade views**
       
       Search for JavaScript code in blade templates that might have hardcoded session durations or prices.
       
       **Files**: 
       - `resources/views/livewire/booking/booking-wizard.blade.php`
       - Any other views with `<script>` tags or x-data
       
       **Changes**: 
       Review inline JavaScript and Alpine.js x-data for hardcoded session arrays. Update to match the new 4-option structure. If prices are referenced in JS, ensure they're passed from PHP rather than hardcoded.
       
       **Verify**: Use browser dev tools to inspect the booking wizard and confirm JavaScript correctly handles the 4 session types.

- [ ] 18. **Final integration test: Create a group booking end-to-end**
       
       Manually test the complete booking flow for a group session to ensure all components work together.
       
       **Files**: N/A (testing step)
       
       **Test procedure**:
       1. Start dev server: `php artisan serve`
       2. Visit the site as a logged-in student
       3. Browse tutors, select one
       4. In the booking wizard, select 60 minutes and "Group Session"
       5. Confirm the price shows KSh 500 (not 700)
       6. Complete the booking (use test payment if Paystack test mode)
       7. Verify the confirmation shows "Group Session"
       8. Visit the booking detail page and confirm session_type is displayed
       9. Check the database: `php artisan tinker`, then `App\Models\Booking::latest()->first()->session_type` should return 'group'
       
       **Verify**: All steps complete successfully. The booking record has session_type = 'group', total_amount = 500, and displays correctly in all views.

---

## Notes

- **Backward Compatibility**: Existing bookings will automatically have session_type = 'individual' due to the migration default. The free first session (20min) logic only applies to individual sessions.

- **Design Decision**: Used composite string keys ("60_individual", "60_group") in sessionPrices() instead of nested arrays to maintain a flat structure that's easy to iterate. This avoids the PHP array key collision problem and keeps view code simple.

- **Price Structure**: Group sessions are intentionally cheaper (500 vs 700) to encourage adoption. Both are 60 minutes but serve different use cases.

- **Verification Strategy**: Each step includes either a tinker command test or a browser-based manual test. Laravel doesn't have automated tests in this project based on the exploration, so all verification is manual.

- **Migration Safety**: The migration adds a column with a default value, so it's safe to run on a database with existing bookings. No data loss risk.
