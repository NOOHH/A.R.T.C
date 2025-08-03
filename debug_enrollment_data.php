<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate a request
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Set up session data (simulating logged-in student)
session([
    'user_id' => 1, // Adjust this to a real student user ID
    'user_role' => 'student',
    'user_name' => 'Test Student',
    'user_firstname' => 'Test',
    'user_lastname' => 'Student'
]);

try {
    // Get student data
    $student = \App\Models\Student::where('user_id', session('user_id'))->first();
    echo "Student found: " . ($student ? "Yes (ID: {$student->student_id})" : "No") . "\n";

    // Get enrollments
    $enrollments = collect();
    
    if (session('user_id')) {
        $userEnrollments = \App\Models\Enrollment::where('user_id', session('user_id'))
            ->with(['program', 'package', 'batch'])
            ->get();
        $enrollments = $enrollments->merge($userEnrollments);
        echo "User enrollments found: " . $userEnrollments->count() . "\n";
    }
    
    if ($student) {
        $studentEnrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
            ->with(['program', 'package', 'batch'])
            ->get();
        $enrollments = $enrollments->merge($studentEnrollments);
        echo "Student enrollments found: " . $studentEnrollments->count() . "\n";
    }

    // Remove duplicates
    $enrollments = $enrollments->unique('enrollment_id');
    echo "Total unique enrollments: " . $enrollments->count() . "\n";

    if ($enrollments->count() > 0) {
        foreach ($enrollments as $enrollment) {
            echo "\nEnrollment ID: {$enrollment->enrollment_id}\n";
            echo "Program: " . ($enrollment->program ? $enrollment->program->program_name : 'No Program') . "\n";
            echo "Program ID: " . ($enrollment->program ? $enrollment->program->program_id : 'N/A') . "\n";
            echo "Package: " . ($enrollment->package ? $enrollment->package->package_name : 'No Package') . "\n";
            echo "Status: {$enrollment->enrollment_status}\n";
            
            // Check for enrollment courses
            $enrollmentCourses = \App\Models\EnrollmentCourse::where('enrollment_id', $enrollment->enrollment_id)
                ->with(['course', 'module'])
                ->get();
            echo "Enrollment courses: " . $enrollmentCourses->count() . "\n";
            
            if ($enrollmentCourses->count() > 0) {
                foreach ($enrollmentCourses as $ec) {
                    if ($ec->course) {
                        echo "  - Course: {$ec->course->subject_name} (ID: {$ec->course->subject_id})\n";
                    }
                }
            } else {
                // Check program modules/courses
                $programModules = \App\Models\Module::where('program_id', $enrollment->program_id)
                    ->where('is_archived', false)
                    ->with(['courses' => function($query) {
                        $query->where('is_archived', false);
                    }])
                    ->get();
                
                echo "Program modules: " . $programModules->count() . "\n";
                foreach ($programModules as $module) {
                    echo "  Module: {$module->module_name} - Courses: " . $module->courses->count() . "\n";
                    foreach ($module->courses as $course) {
                        echo "    - Course: {$course->subject_name} (ID: {$course->subject_id})\n";
                    }
                }
            }
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
