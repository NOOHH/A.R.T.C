<?php
// Debug script to understand the modular enrollment course filtering issue

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Enrollment;
use App\Models\EnrollmentCourse;
use App\Models\Student;
use App\Models\Course;
use App\Models\Module;

echo "=== MODULAR ENROLLMENT COURSE DEBUG ===\n\n";

// Find a modular enrollment
$modularEnrollment = Enrollment::where('enrollment_type', 'Modular')
    ->with(['enrollmentCourses', 'user', 'program'])
    ->first();

if (!$modularEnrollment) {
    echo "No modular enrollments found.\n";
    exit;
}

echo "Found modular enrollment:\n";
echo "- Enrollment ID: {$modularEnrollment->enrollment_id}\n";
echo "- User ID: {$modularEnrollment->user_id}\n";
echo "- Program: {$modularEnrollment->program->program_name}\n";
echo "- Enrollment Type: {$modularEnrollment->enrollment_type}\n";
echo "- Status: {$modularEnrollment->enrollment_status}\n\n";

echo "Enrolled Courses:\n";
foreach ($modularEnrollment->enrollmentCourses as $enrollmentCourse) {
    $course = Course::find($enrollmentCourse->course_id);
    $module = Module::find($enrollmentCourse->module_id);
    
    echo "- Course ID: {$enrollmentCourse->course_id}\n";
    echo "  Course Name: " . ($course ? $course->subject_name : 'Course not found') . "\n";
    echo "  Module ID: {$enrollmentCourse->module_id}\n";
    echo "  Module Name: " . ($module ? $module->module_name : 'Module not found') . "\n";
    echo "  Is Active: " . ($enrollmentCourse->is_active ? 'Yes' : 'No') . "\n";
    echo "  Enrollment Type: {$enrollmentCourse->enrollment_type}\n\n";
}

// Check what the filtering logic returns
$userId = $modularEnrollment->user_id;
$programId = $modularEnrollment->program_id;

echo "=== TESTING FILTERING LOGIC ===\n\n";

// Simulate the current filtering logic from getModuleCourses
$student = Student::where('user_id', $userId)->first();
echo "Student found: " . ($student ? "Yes (ID: {$student->student_id})" : "No") . "\n";

if ($student) {
    $enrollment = $student->enrollments()
        ->where('program_id', $programId)
        ->orderByDesc('enrollment_status')
        ->orderByDesc('created_at')
        ->first();
    
    echo "Enrollment found: " . ($enrollment ? "Yes (ID: {$enrollment->enrollment_id})" : "No") . "\n";
    
    if ($enrollment && $enrollment->enrollment_type === 'Modular') {
        echo "Enrollment is modular: Yes\n";
        
        $allowedCourseIds = $enrollment->enrollmentCourses()->pluck('course_id')->toArray();
        echo "Allowed Course IDs: " . implode(', ', $allowedCourseIds) . "\n";
        
        // Get all courses in the program
        $allProgramModules = Module::where('program_id', $programId)->get();
        echo "\nAll modules in program:\n";
        foreach ($allProgramModules as $module) {
            $allCourses = Course::where('module_id', $module->modules_id)->get();
            echo "- Module: {$module->module_name} (ID: {$module->modules_id})\n";
            foreach ($allCourses as $course) {
                $isAllowed = in_array($course->subject_id, $allowedCourseIds);
                echo "  - Course: {$course->subject_name} (ID: {$course->subject_id}) - " . ($isAllowed ? "ALLOWED" : "BLOCKED") . "\n";
            }
        }
    }
}

echo "\n=== END DEBUG ===\n";
