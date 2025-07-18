<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Module;
use App\Models\Course;
use App\Models\EnrollmentCourse;
use App\Models\Enrollment;

class CourseTestController extends Controller
{
    /**
     * Test the course-level functionality
     */
    public function testCourseAccess(Request $request)
    {
        try {
            // Test 1: Check if we can load packages with courses
            $packages = Package::with(['courses', 'modules'])
                ->where('selection_type', 'course')
                ->get();

            // Test 2: Check if we can load modules with courses
            $modules = Module::with('courses')
                ->where('program_id', 32) // Use the correct program ID
                ->get();

            // Test 3: Check enrollment course creation
            $enrollmentCourses = EnrollmentCourse::with(['course', 'module', 'enrollment'])
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'packages_with_courses' => $packages->count(),
                    'modules_with_courses' => $modules->map(function($module) {
                        return [
                            'module_id' => $module->modules_id,
                            'module_name' => $module->module_name,
                            'courses_count' => $module->courses->count(),
                            'courses' => $module->courses->map(function($course) {
                                return [
                                    'course_id' => $course->subject_id,
                                    'course_name' => $course->subject_name
                                ];
                            })
                        ];
                    }),
                    'enrollment_courses_count' => $enrollmentCourses->count(),
                    'sample_enrollment_courses' => $enrollmentCourses->map(function($ec) {
                        return [
                            'id' => $ec->id,
                            'course_name' => $ec->course->subject_name ?? 'Unknown',
                            'module_name' => $ec->module->module_name ?? 'Unknown',
                            'enrollment_type' => $ec->enrollment_type
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test creating a course-level enrollment
     */
    public function testCreateCourseEnrollment(Request $request)
    {
        try {
            // Get first available module and course for testing
            $module = Module::with('courses')->first();
            if (!$module || $module->courses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'No modules with courses found for testing'
                ]);
            }

            $course = $module->courses->first();

            // Create a test enrollment course record
            $enrollmentCourse = EnrollmentCourse::create([
                'enrollment_id' => 1, // Assume enrollment ID 1 exists
                'course_id' => $course->subject_id,
                'module_id' => $module->modules_id,
                'enrollment_type' => 'course',
                'course_price' => 0,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course enrollment created successfully',
                'data' => [
                    'enrollment_course_id' => $enrollmentCourse->id,
                    'course_name' => $course->subject_name,
                    'module_name' => $module->module_name,
                    'enrollment_type' => $enrollmentCourse->enrollment_type
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
