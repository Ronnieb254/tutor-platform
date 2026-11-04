<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Payment;
use App\Services\DarajaService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::published()->with(['tutor','category'])->latest()->paginate(12);
        return view('pages.courses.index', compact('courses'));
    }

    public function show(string $slug)
    {
        $course   = Course::published()->where('slug', $slug)->with(['tutor','lessons','category'])->firstOrFail();
        $enrolled = auth()->check() && $course->isEnrolledBy(auth()->user());
        return view('pages.courses.show', compact('course', 'enrolled'));
    }

    public function enroll(Course $course, Request $request, DarajaService $daraja)
    {
        if ($course->is_free || $course->price == 0) {
            CourseEnrollment::firstOrCreate([
                'student_id' => auth()->id(),
                'course_id'  => $course->id,
            ]);
            return back()->with('success', 'Enrolled successfully!');
        }

        $request->validate(['phone' => 'required|string|min:9|max:15']);

        $reference = $daraja->generateReference('CRS');

        $result = $daraja->initializeStkPush(
            $request->input('phone'),
            $course->price,
            $reference,
            'Mwalimu Course'
        );

        $responseCode = $result['ResponseCode'] ?? ($result['errorCode'] ?? null);

        if ($responseCode === '0' || isset($result['_mock'])) {
            Payment::create([
                'user_id'             => auth()->id(),
                'course_id'           => $course->id,
                'amount'              => $course->price,
                'currency'            => 'KES',
                'payment_type'        => 'course',
                'mpesa_reference'     => $reference,
                'checkout_request_id' => $result['CheckoutRequestID'] ?? null,
                'payer_phone'         => $daraja->normalizePhone($request->input('phone')),
                'status'              => 'pending',
            ]);

            return back()->with('success', 'Check your phone for the M-Pesa prompt. You will be enrolled once payment is confirmed.');
        }

        return back()->with('error', 'Could not process payment: ' . ($result['errorMessage'] ?? 'Please try again.'));
    }
}
