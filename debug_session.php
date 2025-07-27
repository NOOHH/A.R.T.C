<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Session debugging:\n";
echo "User ID: " . (session('user_id') ?? 'Not set') . "\n";
echo "User Name: " . (session('user_name') ?? 'Not set') . "\n";
echo "User Role: " . (session('user_role') ?? 'Not set') . "\n";

// Check if we can manually test the API logic
$userId = session('user_id') ?? 174; // Use 174 as fallback since I saw it in the data

echo "\nTesting enrolled courses for User ID $userId:\n";

$enrolledCourseIds = \App\Models\EnrollmentCourse::whereHas('enrollment', function($query) use ($userId) {
    $query->where('user_id', $userId)
          ->where('enrollment_status', '!=', 'rejected');
})->where('is_active', true)
  ->pluck('course_id')
  ->toArray();

echo "Enrolled Course IDs: " . implode(', ', $enrolledCourseIds) . "\n";

// Get course names
foreach($enrolledCourseIds as $courseId) {
    $course = \App\Models\Course::find($courseId);
    if($course) {
        echo "Course ID $courseId: " . $course->subject_name . "\n";
    }
}

// Test getting programs and see if Civil Engineering courses are marked as enrolled
echo "\nTesting program API logic:\n";
$programs = \App\Models\Program::with(['modules.courses'])
    ->where('is_archived', false)
    ->get();

foreach($programs as $program) {
    if($program->program_name === 'Civil Engineering') {
        echo "Found Civil Engineering program (ID: {$program->program_id}):\n";
        foreach($program->modules as $module) {
            echo "  Module: {$module->module_name} (ID: {$module->modules_id})\n";
            foreach($module->courses as $course) {
                $isEnrolled = in_array($course->subject_id, $enrolledCourseIds);
                echo "    Course: {$course->subject_name} (ID: {$course->subject_id}) - " . 
                     ($isEnrolled ? "ALREADY ENROLLED" : "Available") . "\n";
            }
        }
    }
}
