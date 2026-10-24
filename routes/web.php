<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/how-it-works', fn() => view('pages.how-it-works'))->name('how-it-works');
Route::get('/examiner-clinic', [App\Http\Controllers\ClinicController::class, 'index'])->name('examiner-clinic');
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
        Route::get('/dashboard/student', fn() => view('pages.dashboard.student'))->name('student.dashboard');
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
    });

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
        
        // Document view (secure)
        Route::get('/tutor/documents/{document}/view', [App\Http\Controllers\TutorController::class, 'viewDocument'])->name('tutor.documents.view');

        // Availability
        Route::post('/tutor/availability', [App\Http\Controllers\TutorController::class, 'updateAvailability'])->name('tutor.availability.update');

        // Profile updates
        Route::patch('/tutor/profile/update', [App\Http\Controllers\TutorController::class, 'updatePersonal'])->name('tutor.profile.update');
        Route::patch('/tutor/profile/tutor-update', [App\Http\Controllers\TutorController::class, 'updateTutorProfile'])->name('tutor.profile.tutor-update');

        // Payout request
        Route::post('/tutor/payouts', [App\Http\Controllers\TutorController::class, 'requestPayout'])->name('tutor.payouts.store');

        // Notifications mark read
        Route::post('/tutor/notifications/mark-all-read', [App\Http\Controllers\TutorController::class, 'markNotificationsRead'])->name('tutor.notifications.read');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/',           fn() => view('pages.dashboard.admin'))->name('dashboard');
        Route::get('/tutors',     fn() => view('pages.admin.tutors'))->name('tutors');
        Route::get('/users',      fn() => view('pages.admin.users'))->name('users');
        Route::get('/payouts',    fn() => view('pages.admin.payouts'))->name('payouts');
        Route::get('/categories', fn() => view('pages.admin.categories'))->name('categories');
        Route::get('/settings',   fn() => view('pages.admin.settings'))->name('settings');
        Route::get('/blog',       fn() => view('pages.admin.blog'))->name('blog');

        // Tutor approve / suspend
        Route::patch('/tutors/{profile}/approve',  [App\Http\Controllers\Admin\TutorApprovalController::class, 'approve'])->name('tutors.approve');
        Route::patch('/tutors/{profile}/suspend',  [App\Http\Controllers\Admin\TutorApprovalController::class, 'suspend'])->name('tutors.suspend');

        // Document approve / reject
        Route::patch('/documents/{document}/approve', [App\Http\Controllers\Admin\TutorApprovalController::class, 'approveDoc'])->name('documents.approve');
        Route::patch('/documents/{document}/reject',  [App\Http\Controllers\Admin\TutorApprovalController::class, 'rejectDoc'])->name('documents.reject');

        // User toggle active
        Route::patch('/users/{user}/toggle-active', [App\Http\Controllers\Admin\TutorApprovalController::class, 'toggleUser'])->name('users.toggle');
    });
});

// ─── Paystack Callback (redirected from Paystack, not a webhook) ──────────────
Route::get('/paystack/callback', function () {
    return redirect()->route('student.dashboard')->with('info', 'Payment processed.');
})->name('paystack.callback');
