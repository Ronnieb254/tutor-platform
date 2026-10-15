<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Payment;
use App\Services\PaystackService;
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

    public function enroll(Course $course, Request $request, PaystackService $paystack)
    {
        if ($course->is_free || $course->price == 0) {
            CourseEnrollment::firstOrCreate([
                'student_id' => auth()->id(),
                'course_id'  => $course->id,
            ]);
            return back()->with('success', 'Enrolled successfully!');
        }

        $reference = $paystack->generateReference('CRS');
        $result    = $paystack->initializeTransaction(auth()->user()->email, $course->price, $reference, ['course_id' => $course->id]);

        if ($result['status'] ?? false) {
            return response()->json(['access_code' => $result['data']['access_code'], 'reference' => $reference]);
        }

        return back()->with('error', 'Could not process payment.');
    }
}
