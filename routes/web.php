<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/how-it-works', [HomeController::class, 'howItWorks'])->name('how-it-works');
Route::get('/examiner-clinic', [App\Http\Controllers\ClinicController::class, 'index'])->name('examiner-clinic');
Route::get('/group-sessions', [App\Http\Controllers\GroupSessionController::class, 'index'])->name('group-sessions.index');
// Constrain to numeric IDs so the static /my-sessions route is never swallowed by this wildcard
Route::get('/group-sessions/{groupSession}', [App\Http\Controllers\GroupSessionController::class, 'show'])->name('group-sessions.show')->whereNumber('groupSession');
Route::get('/contact', fn() => view('pages.contact'))->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

// ─── Authenticated Tutor Listings ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/tutors', [HomeController::class, 'tutors'])->name('tutors.index');
    Route::get('/tutors/{id}', [HomeController::class, 'tutorProfile'])->name('tutors.show')->where('id', '[0-9]+');
    Route::get('/tutors/{slug}', [HomeController::class, 'tutorProfileBySlug'])->name('tutors.show.slug');
});
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ─── Library (replaces Blog) ──────────────────────────────────────────────────
Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
Route::get('/library/{slug}', [LibraryController::class, 'show'])->name('library.show');
Route::middleware('auth')->get('/library/{slug}/download', [LibraryController::class, 'download'])->name('library.download');

// ─── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [App\Http\Controllers\Auth\LoginController::class, 'show'])->name('login');
    Route::post('/login',   [App\Http\Controllers\Auth\LoginController::class, 'store']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'show'])->name('register');
    Route::post('/register',[App\Http\Controllers\Auth\RegisterController::class, 'store']);
});
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// ─── Social OAuth ─────────────────────────────────────────────────────────────
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('auth.social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.social.callback');

// ─── Authenticated ────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Clinic enrollment (KSh 300 per session, 30 students max)
    Route::post('/clinic/{clinic}/enroll', [App\Http\Controllers\ClinicController::class, 'enroll'])->name('clinic.enroll');

    // Group session enrollment (multi-student classes)
    Route::post('/group-sessions/{groupSession}/enroll', [App\Http\Controllers\GroupSessionController::class, 'enroll'])->name('group-sessions.enroll');
    Route::get('/group-sessions/my-sessions', [App\Http\Controllers\GroupSessionController::class, 'mySessions'])->name('group-sessions.my-sessions');

    // Clinic booking & payment flow
    Route::get('/clinic/{clinic}/checkout', [App\Http\Controllers\ClinicBookingController::class, 'checkout'])->name('clinic.checkout');
    Route::post('/clinic/{clinic}/pay', [App\Http\Controllers\ClinicBookingController::class, 'pay'])->name('clinic.pay');
    Route::get('/clinic/callback', [App\Http\Controllers\ClinicBookingController::class, 'callback'])->name('clinic.callback');

    // Clinic session access (platform-only)
    Route::get('/clinic/my-bookings', [App\Http\Controllers\ClinicBookingController::class, 'myBookings'])->name('clinic.my-bookings');
    Route::get('/clinic/{clinic}/join', [App\Http\Controllers\ClinicBookingController::class, 'joinSession'])->name('clinic.join');
    Route::get('/clinic/session', [App\Http\Controllers\ClinicBookingController::class, 'sessionRoom'])->name('clinic.session-room');

    // Examiner (verified tutors creating clinics)
    Route::get('/examiner/apply', [App\Http\Controllers\ExaminerController::class, 'showApplyForm'])->name('examiner.apply');
    Route::post('/examiner/apply', [App\Http\Controllers\ExaminerController::class, 'apply']);
    Route::get('/examiner/dashboard', [App\Http\Controllers\ExaminerController::class, 'dashboard'])->name('examiner.dashboard');
    Route::get('/examiner/clinics/create', [App\Http\Controllers\ExaminerController::class, 'showCreateForm'])->name('examiner.clinics.create');
    Route::post('/examiner/clinics', [App\Http\Controllers\ExaminerController::class, 'createClinic'])->name('examiner.clinics.store');
    Route::get('/examiner/clinics/{clinic}', [App\Http\Controllers\ExaminerController::class, 'showClinic'])->name('examiner.clinics.show');
    Route::get('/examiner/clinics/{clinic}/join', [App\Http\Controllers\ExaminerController::class, 'joinAsExaminer'])->name('examiner.clinics.join');

    // Bookings
    Route::get('/bookings/{booking}',         [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings',                  [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/confirm',          [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/joined', [BookingController::class, 'markJoined'])->name('bookings.joined');

    // Booking messages
    Route::post('/bookings/{booking}/messages', function(\Illuminate\Http\Request $req, \App\Models\Booking $booking) {
        $req->validate(['body' => 'required|string|max:1000']);
        $receiver = auth()->id() === $booking->student_id ? $booking->tutor_id : $booking->student_id;
        $msg = $booking->messages()->create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $receiver,
            'body'        => $req->body,
        ]);
        return response()->json(['status'=>'ok','id'=>$msg->id]);
    })->name('bookings.messages.store');

    // Booking review
    Route::post('/bookings/{booking}/review', function(\Illuminate\Http\Request $req, \App\Models\Booking $booking) {
        $req->validate(['rating'=>'required|integer|min:1|max:5','comment'=>'nullable|string|max:1000']);
        $booking->review()->create([
            'student_id' => auth()->id(),
            'tutor_id'   => $booking->tutor_id,
            'rating'     => $req->rating,
            'comment'    => $req->comment,
            'is_visible' => true,
        ]);
        // Update tutor rating avg
        $profile = \App\Models\TutorProfile::where('user_id', $booking->tutor_id)->first();
        if ($profile) {
            $avg = $profile->reviews()->where('is_visible',true)->avg('rating');
            $profile->update(['rating_avg'=>round($avg,2),'total_reviews'=>$profile->reviews()->where('is_visible',true)->count()]);
        }
        return response()->json(['status'=>'ok']);
    })->name('bookings.review.store');

    // Course enrollment
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');

    // Sessions
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [App\Http\Controllers\SessionController::class, 'index'])->name('index');
        Route::get('/history', [App\Http\Controllers\SessionController::class, 'history'])->name('history');
        Route::get('/{booking:uuid}', [App\Http\Controllers\SessionController::class, 'show'])->name('show');
        Route::get('/{booking:uuid}/join', [App\Http\Controllers\SessionController::class, 'join'])->name('join');
        Route::post('/{booking:uuid}/confirm', [App\Http\Controllers\SessionController::class, 'confirm'])->name('confirm');
        Route::post('/{booking:uuid}/cancel', [App\Http\Controllers\SessionController::class, 'cancel'])->name('cancel');
        Route::post('/{booking:uuid}/complete', [App\Http\Controllers\SessionController::class, 'complete'])->name('complete');
    });

    // Messages
    Route::prefix('sessions/{booking:uuid}/messages')->name('messages.')->group(function () {
        Route::get('/', [App\Http\Controllers\MessageController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\MessageController::class, 'store'])->name('store');
        Route::post('/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('read');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [App\Http\Controllers\NotificationController::class, 'unread'])->name('unread');
        Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('readAll');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    });

    // Direct Messages
    Route::get('/inbox', [App\Http\Controllers\MessagesController::class, 'index'])->name('inbox.index');
    Route::get('/api/messages/{userId}', [App\Http\Controllers\MessagesController::class, 'getMessages']);
    Route::post('/api/messages/{userId}', [App\Http\Controllers\MessagesController::class, 'sendMessage']);

    // Student dashboard
    Route::middleware('role:student,parent')->group(function () {
        Route::get('/dashboard/student', [App\Http\Controllers\StudentDashboardController::class, 'index'])->name('student.dashboard');
        Route::post('/student/notifications/mark-all-read', function () {
            auth()->user()->unreadNotifications()->update(['read_at' => now()]);
            return back()->with('success', 'All notifications marked as read.');
        })->name('student.notifications.read');
        Route::post('/student/children', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'nullable|email|unique:users,email',
                'age' => 'nullable|integer|min:3|max:25',
                'grade_level' => 'nullable|string|max:50',
            ]);
            
            // Generate a unique email if not provided
            $email = $request->email;
            if (!$email) {
                $slug = \Illuminate\Support\Str::slug($request->name);
                $random = \Illuminate\Support\Str::random(6);
                $email = strtolower($slug . '_' . $random . '@child.mwalimu.local');
            }
            
            // Create child as a User
            auth()->user()->children()->create([
                'name' => $request->name,
                'email' => $email,
                'password' => bcrypt(\Illuminate\Support\Str::random(32)), // Random password
                'role' => 'student',
                'age' => $request->age,
                'grade_level' => $request->grade_level,
                'is_active' => true,
            ]);
            
            return back()->with('success', 'Child added successfully.');
        })->name('student.children.store');
        
        // Refund requests (student/parent only)
        Route::post('/bookings/{booking}/refund/first-20min', [App\Http\Controllers\RefundController::class, 'requestFirst20MinRefund'])->name('bookings.refund.first20min');
        Route::post('/bookings/{booking}/refund', [App\Http\Controllers\RefundController::class, 'requestRefund'])->name('bookings.refund.request');
    });

    // Document view (secure) - accessible by tutors (own docs) and admins (all docs)
    Route::get('/tutor/documents/{document}/view', [App\Http\Controllers\TutorController::class, 'viewDocument'])->name('tutor.documents.view');

    // Tutor dashboard & onboarding
    Route::middleware('role:tutor')->group(function () {
        Route::get('/dashboard/tutor', function () {
            $earningsService = new \App\Services\EarningsService();
            $allEarnings = $earningsService->calculateTutorEarnings(auth()->user());
            $recentEarnings = $earningsService->getRecentEarnings(auth()->user());
            $earnings = array_merge($allEarnings, $recentEarnings);
            
            return view('pages.dashboard.tutor', compact('earnings'));
        })->name('tutor.dashboard');
        Route::get('/tutor/onboarding', fn() => view('pages.auth.tutor-onboarding'))->name('tutor.onboarding');

        // New onboarding submit (file uploads + speedy verification)
        Route::post('/tutor/onboarding/submit', [App\Http\Controllers\TutorOnboardingController::class, 'submit'])->name('tutor.onboarding.submit');

        // Document upload
        Route::post('/tutor/documents/upload', [App\Http\Controllers\TutorController::class, 'uploadDocument'])->name('tutor.documents.upload');

        // Availability
        Route::post('/tutor/availability', [App\Http\Controllers\TutorController::class, 'updateAvailability'])->name('tutor.availability.update');

        // Profile updates
        Route::patch('/tutor/profile/update', [App\Http\Controllers\TutorController::class, 'updatePersonal'])->name('tutor.profile.update');
        Route::patch('/tutor/profile/tutor-update', [App\Http\Controllers\TutorController::class, 'updateTutorProfile'])->name('tutor.profile.tutor-update');

        // Payout request
        Route::post('/tutor/payouts', [App\Http\Controllers\TutorController::class, 'requestPayout'])->name('tutor.payouts.store');

        // Notifications mark read
        Route::post('/tutor/notifications/mark-all-read', [App\Http\Controllers\TutorController::class, 'markNotificationsRead'])->name('tutor.notifications.read');

        // Group sessions (tutor creates & manages multi-student classes)
        Route::get('/tutor/group-sessions',                          [App\Http\Controllers\Tutor\GroupSessionController::class, 'index'])->name('tutor.group-sessions.index');
        Route::get('/tutor/group-sessions/create',                   [App\Http\Controllers\Tutor\GroupSessionController::class, 'create'])->name('tutor.group-sessions.create');
        Route::post('/tutor/group-sessions',                         [App\Http\Controllers\Tutor\GroupSessionController::class, 'store'])->name('tutor.group-sessions.store');
        Route::get('/tutor/group-sessions/{groupSession}',          [App\Http\Controllers\Tutor\GroupSessionController::class, 'show'])->name('tutor.group-sessions.show')->whereNumber('groupSession');
        Route::post('/tutor/group-sessions/{groupSession}/cancel', [App\Http\Controllers\Tutor\GroupSessionController::class, 'cancel'])->name('tutor.group-sessions.cancel')->whereNumber('groupSession');

        // Library resources (tutor uploads & manages their own resources)
        Route::get('/tutor/library',          [App\Http\Controllers\Tutor\LibraryController::class, 'index'])->name('tutor.library.index');
        Route::get('/tutor/library/create',   [App\Http\Controllers\Tutor\LibraryController::class, 'create'])->name('tutor.library.create');
        Route::post('/tutor/library',         [App\Http\Controllers\Tutor\LibraryController::class, 'store'])->name('tutor.library.store');
        Route::delete('/tutor/library/{post}', [App\Http\Controllers\Tutor\LibraryController::class, 'destroy'])->name('tutor.library.destroy');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/',           fn() => view('pages.dashboard.admin'))->name('dashboard');
        Route::get('/tutors',     fn() => view('pages.admin.tutors'))->name('tutors');
        Route::get('/users',      fn() => view('pages.admin.users'))->name('users');
        Route::get('/payouts',    fn() => view('pages.admin.payouts'))->name('payouts');
        Route::get('/settings',   fn() => view('pages.admin.settings'))->name('settings');
        Route::get('/blog',       fn() => view('pages.admin.blog'))->name('blog');

        // Library management (replaces blog admin)
        Route::get('/library',                                           [App\Http\Controllers\Admin\LibraryController::class, 'index'])->name('library');
        Route::post('/library',                                          [App\Http\Controllers\Admin\LibraryController::class, 'store'])->name('library.store');
        Route::post('/library/{post}/toggle',                            [App\Http\Controllers\Admin\LibraryController::class, 'toggle'])->name('library.toggle');
        Route::post('/library/{post}/approve',                           [App\Http\Controllers\Admin\LibraryController::class, 'approve'])->name('library.approve');
        Route::delete('/library/{post}',                                 [App\Http\Controllers\Admin\LibraryController::class, 'destroy'])->name('library.destroy');
        Route::post('/library/categories',                               [App\Http\Controllers\Admin\LibraryController::class, 'storeCategory'])->name('library.categories.store');
        Route::delete('/library/categories/{category}',                  [App\Http\Controllers\Admin\LibraryController::class, 'destroyCategory'])->name('library.categories.destroy');

        // Categories management
        Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::patch('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/{category}/toggle', [App\Http\Controllers\Admin\CategoryController::class, 'toggle'])->name('categories.toggle');

        // Subjects management
        Route::get('/subjects', [App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('subjects');
        Route::post('/subjects', [App\Http\Controllers\Admin\SubjectController::class, 'store'])->name('subjects.store');
        Route::patch('/subjects/{subject}', [App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [App\Http\Controllers\Admin\SubjectController::class, 'destroy'])->name('subjects.destroy');
        Route::post('/subjects/{subject}/toggle', [App\Http\Controllers\Admin\SubjectController::class, 'toggle'])->name('subjects.toggle');

        // Tutor approve / suspend
        Route::patch('/tutors/{profile}/approve',  [App\Http\Controllers\Admin\TutorApprovalController::class, 'approve'])->name('tutors.approve');
        Route::patch('/tutors/{profile}/suspend',  [App\Http\Controllers\Admin\TutorApprovalController::class, 'suspend'])->name('tutors.suspend');

        // Document approve / reject
        Route::patch('/documents/{document}/approve', [App\Http\Controllers\Admin\TutorApprovalController::class, 'approveDoc'])->name('documents.approve');
        Route::patch('/documents/{document}/reject',  [App\Http\Controllers\Admin\TutorApprovalController::class, 'rejectDoc'])->name('documents.reject');

        // User toggle active
        Route::patch('/users/{user}/toggle-active', [App\Http\Controllers\Admin\TutorApprovalController::class, 'toggleUser'])->name('users.toggle');
        
        // Refund management
        Route::post('/refunds/{refund}/approve', [App\Http\Controllers\RefundController::class, 'approve'])->name('refunds.approve');
        Route::post('/refunds/{refund}/reject', [App\Http\Controllers\RefundController::class, 'reject'])->name('refunds.reject');
        
        // Tutor subjects management (admin can manage any tutor's subjects)
        Route::get('/tutors/{tutorProfile}/subjects', [App\Http\Controllers\TutorSubjectController::class, 'index'])->name('tutors.subjects');
        Route::post('/tutors/{tutorProfile}/subjects', [App\Http\Controllers\TutorSubjectController::class, 'store'])->name('tutors.subjects.store');
        Route::post('/tutors/{tutorProfile}/subjects/attach', [App\Http\Controllers\TutorSubjectController::class, 'attach'])->name('tutors.subjects.attach');
        Route::post('/tutors/{tutorProfile}/subjects/detach', [App\Http\Controllers\TutorSubjectController::class, 'detach'])->name('tutors.subjects.detach');

        // Clinic management (admin creates / manages daily clinics)
        Route::get('/clinics',                             [App\Http\Controllers\Admin\ClinicController::class, 'index'])->name('clinics');
        Route::get('/clinics/create',                      [App\Http\Controllers\Admin\ClinicController::class, 'create'])->name('clinics.create');
        Route::post('/clinics',                            [App\Http\Controllers\Admin\ClinicController::class, 'store'])->name('clinics.store');
        Route::get('/clinics/{clinic}',                    [App\Http\Controllers\Admin\ClinicController::class, 'show'])->name('clinics.show');
        Route::post('/clinics/{clinic}/cancel',            [App\Http\Controllers\Admin\ClinicController::class, 'cancel'])->name('clinics.cancel');
        Route::post('/clinics/{clinic}/create-overflow',   [App\Http\Controllers\Admin\ClinicController::class, 'createOverflow'])->name('clinics.overflow');
        Route::delete('/clinics/{clinic}',                 [App\Http\Controllers\Admin\ClinicController::class, 'destroy'])->name('clinics.destroy');
        
        // Examiner Clinics management
        Route::get('/examiner-clinics', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'index'])->name('examiner-clinics.index');
        Route::get('/examiner-clinics/create', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'create'])->name('examiner-clinics.create');
        Route::post('/examiner-clinics', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'store'])->name('examiner-clinics.store');
        Route::get('/examiner-clinics/{examinerClinic}', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'show'])->name('examiner-clinics.show');
        Route::get('/examiner-clinics/{examinerClinic}/edit', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'edit'])->name('examiner-clinics.edit');
        Route::patch('/examiner-clinics/{examinerClinic}', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'update'])->name('examiner-clinics.update');
        Route::delete('/examiner-clinics/{examinerClinic}', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'destroy'])->name('examiner-clinics.destroy');
        Route::post('/examiner-clinics/{examinerClinic}/cancel', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'cancel'])->name('examiner-clinics.cancel');
        Route::post('/examiner-clinics/{examinerClinic}/start', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'start'])->name('examiner-clinics.start');
        Route::post('/examiner-clinics/{examinerClinic}/complete', [App\Http\Controllers\Admin\ExaminerClinicController::class, 'complete'])->name('examiner-clinics.complete');
    });
    
    // Tutor can manage their own subjects
    Route::middleware('role:tutor')->group(function () {
        Route::get('/my-subjects', function() {
            $tutorProfile = auth()->user()->tutorProfile;
            if (!$tutorProfile) abort(404, 'Tutor profile not found');
            return app(App\Http\Controllers\TutorSubjectController::class)->index($tutorProfile);
        })->name('tutor.subjects');
        Route::post('/my-subjects', function(\Illuminate\Http\Request $request) {
            $tutorProfile = auth()->user()->tutorProfile;
            if (!$tutorProfile) abort(404, 'Tutor profile not found');
            return app(App\Http\Controllers\TutorSubjectController::class)->store($request, $tutorProfile);
        })->name('tutor.subjects.store');
        Route::post('/my-subjects/attach', function(\Illuminate\Http\Request $request) {
            $tutorProfile = auth()->user()->tutorProfile;
            if (!$tutorProfile) abort(404, 'Tutor profile not found');
            return app(App\Http\Controllers\TutorSubjectController::class)->attach($request, $tutorProfile);
        })->name('tutor.subjects.attach');
        Route::post('/my-subjects/detach', function(\Illuminate\Http\Request $request) {
            $tutorProfile = auth()->user()->tutorProfile;
            if (!$tutorProfile) abort(404, 'Tutor profile not found');
            return app(App\Http\Controllers\TutorSubjectController::class)->detach($request, $tutorProfile);
        })->name('tutor.subjects.detach');
    });
    
    // Examiner Clinic Enrollment (student-facing)
    Route::get('/examiner-clinics', [App\Http\Controllers\ExaminerClinicEnrollmentController::class, 'index'])->name('examiner-clinics.index');
    Route::get('/examiner-clinics/my-enrollments', [App\Http\Controllers\ExaminerClinicEnrollmentController::class, 'myEnrollments'])->name('examiner-clinics.my-enrollments');
    Route::get('/examiner-clinics/{examinerClinic}', [App\Http\Controllers\ExaminerClinicEnrollmentController::class, 'show'])->name('examiner-clinics.show');
    Route::post('/examiner-clinics/{examinerClinic}/enroll', [App\Http\Controllers\ExaminerClinicEnrollmentController::class, 'enroll'])->name('examiner-clinics.enroll');
    Route::post('/examiner-clinics/{examinerClinic}/unenroll', [App\Http\Controllers\ExaminerClinicEnrollmentController::class, 'unenroll'])->name('examiner-clinics.unenroll');
    Route::get('/examiner-clinics/{examinerClinic}/join', [App\Http\Controllers\ExaminerClinicEnrollmentController::class, 'join'])->name('examiner-clinics.join');
});

// ─── Paystack Callback (redirected from Paystack, not a webhook) ──────────────
Route::get('/paystack/callback', function () {
    return redirect()->route('student.dashboard')->with('info', 'Payment processed.');
})->name('paystack.callback');
