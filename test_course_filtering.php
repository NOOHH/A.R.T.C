<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\StudentDashboardController;
use App\Models\EnrollmentCourse;
use App\Models\Student;

echo "Testing course filtering for student 2025-07-00001 in module 66...\n\n";

// First, let's find the correct user_id for student 2025-07-00001
$student = Student::where('student_id', '2025-07-00001')->first();
if ($student) {
    echo "Found student - User ID: {$student->user_id}, Student ID: {$student->student_id}\n";
    // Simulate session data
    session(['user_id' => $student->user_id]);
} else {
    echo "Student 2025-07-00001 not found!\n";
    exit;
}

// Check enrollment courses first
$enrollmentCourses = EnrollmentCourse::where('enrollment_id', 166)->get();
echo "Enrollment courses for enrollment 166:\n";
foreach ($enrollmentCourses as $ec) {
    echo "- Course ID: {$ec->course_id}, Module ID: {$ec->module_id}\n";
}

echo "\n";

// Test the controller method
$moduleId = 66;
$controller = new StudentDashboardController();

try {
    $response = $controller->getModuleCourses($moduleId);
    echo "Filtered courses result:\n";
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData();
        $courses = $data->courses ?? [];
        echo "Course count: " . count($courses) . "\n";
        
        if (count($courses) > 0) {
            foreach ($courses as $course) {
                echo "- Course: " . json_encode($course) . "\n";
            }
        } else {
            echo "No courses returned!\n";
        }
        
        echo "Full response data:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Unexpected response type: " . get_class($response) . "\n";
        var_dump($response);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
