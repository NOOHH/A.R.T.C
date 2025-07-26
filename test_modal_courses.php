<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\EnrollmentCourse;

// Simulate Laravel's environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Modal Course Loading ===\n\n";

// Test user 174 with module 6 (Civil Engineering)
$userId = 174;
$moduleId = 6;

echo "Testing for User ID: $userId, Module ID: $moduleId\n";
echo "Expected: Courses 43, 44, 45 should show as already_enrolled = true\n\n";

// Check what courses exist in module 6
$courses = DB::table('courses')
    ->where('module_id', $moduleId)
    ->where('is_active', 1)
    ->get();

echo "Found " . count($courses) . " active courses in module $moduleId:\n";

// Get enrolled course IDs using the correct relationship
$enrolledCourseIds = [];
if ($userId) {
    $enrolledCourseIds = EnrollmentCourse::whereHas('enrollment', function($query) use ($userId) {
        $query->where('user_id', $userId)
              ->where('enrollment_status', '!=', 'rejected');
    })->where('is_active', true)
      ->pluck('course_id')
      ->toArray();
}

echo "User $userId enrolled course IDs: " . implode(', ', $enrolledCourseIds) . "\n\n";

foreach ($courses as $course) {
    $isEnrolled = in_array($course->subject_id, $enrolledCourseIds);
    
    echo "- Course ID: {$course->subject_id}, Name: {$course->subject_name}\n";
    echo "  Already Enrolled: " . ($isEnrolled ? "YES" : "NO") . "\n";
    echo "  Expected in API: already_enrolled: " . ($isEnrolled ? "true" : "false") . "\n\n";
}

// Test the actual API endpoint
echo "=== Testing Actual API Call ===\n";
$client = new \GuzzleHttp\Client();
try {
    $response = $client->get("http://localhost/A.R.T.C/get-module-courses", [
        'query' => [
            'module_id' => $moduleId,
            'user_id' => $userId
        ]
    ]);
    
    $data = json_decode($response->getBody(), true);
    
    echo "API Response:\n";
    echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "Course count: " . count($data['courses'] ?? []) . "\n\n";
    
    foreach ($data['courses'] ?? [] as $course) {
        echo "Course: {$course['course_name']} (ID: {$course['course_id']})\n";
        echo "Already Enrolled: " . ($course['already_enrolled'] ? 'YES' : 'NO') . "\n\n";
    }
    
} catch (Exception $e) {
    echo "API call failed: " . $e->getMessage() . "\n";
}

echo "\n=== Modal Should Display ===\n";
echo "✓ Courses with 'Already Enrolled' badges for enrolled courses\n";
echo "✓ Disabled checkboxes for enrolled courses\n";
echo "✓ Gray/striped styling for enrolled courses\n";
echo "✓ Warning text: 'You cannot enroll in this course again'\n";
?>
