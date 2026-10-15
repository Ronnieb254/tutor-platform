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
Route::get('/examiner-clinic', fn() => view('pages.examiner-clinic'))->name('examiner-clinic');
Route::get('/contact', fn() => view('pages.contact'))->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');
Route::get('/tutors', [HomeController::class, 'tutors'])->name('tutors.index');
Route::get('/tutors/{id}', [HomeController::class, 'tutorProfile'])->name('tutors.show')->where('id', '[0-9]+');
Route::get('/tutors/{slug}', [HomeController::class, 'tutorProfileBySlug'])->name('tutors.show.slug');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ─── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Social OAuth ─────────────────────────────────────────────────────────────
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('auth.social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.social.callback');

// ─── Authenticated ────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

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
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Course enrollment
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');

    // Student dashboard
    Route::middleware('role:student,parent')->group(function () {
        Route::get('/dashboard/student', fn() => view('pages.dashboard.student'))->name('student.dashboard');
        Route::post('/student/notifications/mark-all-read', function () {
            auth()->user()->unreadNotifications()->update(['read_at' => now()]);
            return back()->with('success', 'All notifications marked as read.');
        })->name('student.notifications.read');
        Route::post('/student/children', function (\Illuminate\Http\Request $request) {
            $request->validate(['name' => 'required|string|max:100', 'age' => 'nullable|integer|min:3|max:25', 'grade_level' => 'nullable|string|max:50', 'notes' => 'nullable|string|max:500']);
            auth()->user()->childProfiles()->create($request->only('name','age','grade_level','notes'));
            return back()->with('success', 'Child profile added.');
        })->name('student.children.store');
    });

    // Tutor dashboard & onboarding
    Route::middleware('role:tutor')->group(function () {
        Route::get('/dashboard/tutor', fn() => view('pages.dashboard.tutor'))->name('tutor.dashboard');
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
