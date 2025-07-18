<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\EnrollmentCourse;
use App\Models\User;
use App\Models\Student;

class CheckCourseAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to students
        if (!auth()->check() || auth()->user()->role !== 'student') {
            return $next($request);
        }

        $user = auth()->user();
        $courseId = $request->route('course_id') ?? $request->route('id') ?? $request->get('course_id');

        if ($courseId) {
            // Check if student is enrolled in this specific course
            $hasAccess = EnrollmentCourse::where('course_id', $courseId)
                ->whereHas('enrollment', function ($query) use ($user) {
                    $query->where('user_id', $user->user_id)
                          ->where('enrollment_status', 'active');
                })
                ->where('is_active', true)
                ->exists();

            if (!$hasAccess) {
                // Check if they have module-level access (enrolled in entire module)
                $hasModuleAccess = EnrollmentCourse::where('course_id', $courseId)
                    ->where('enrollment_type', 'module')
                    ->whereHas('enrollment', function ($query) use ($user) {
                        $query->where('user_id', $user->user_id)
                              ->where('enrollment_status', 'active');
                    })
                    ->where('is_active', true)
                    ->exists();

                if (!$hasModuleAccess) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'Access denied. You are not enrolled in this course.',
                            'enrolled_courses' => $this->getEnrolledCourses($user)
                        ], 403);
                    }

                    return redirect()->route('student.dashboard')
                        ->with('error', 'Access denied. You are not enrolled in this course.');
                }
            }
        }

        return $next($request);
    }

    /**
     * Get list of courses the student is enrolled in
     */
    private function getEnrolledCourses($user)
    {
        return EnrollmentCourse::with(['course', 'module'])
            ->whereHas('enrollment', function ($query) use ($user) {
                $query->where('user_id', $user->user_id)
                      ->where('enrollment_status', 'active');
            })
            ->where('is_active', true)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'course_id' => $enrollment->course_id,
                    'course_name' => $enrollment->course->subject_name ?? 'Unknown Course',
                    'module_name' => $enrollment->module->module_name ?? 'Unknown Module',
                    'enrollment_type' => $enrollment->enrollment_type
                ];
            });
    }
}
