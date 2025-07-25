<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\StudentDashboardController;
use App\Models\Module;
use App\Models\Enrollment;
use App\Models\EnrollmentCourse;

// Set up session to simulate the logged-in student
session(['user_id' => 174]);

echo "Testing module filtering for Mechanical Engineer program (ID: 38)...\n\n";

// Get enrollment 166
$enrollment = Enrollment::find(166);
echo "Enrollment 166: Program {$enrollment->program_id}, Type: {$enrollment->enrollment_type}\n";

// Check enrollment courses
$enrollmentCourses = EnrollmentCourse::where('enrollment_id', 166)->get();
echo "Enrollment courses:\n";
foreach ($enrollmentCourses as $ec) {
    echo "- Course ID: {$ec->course_id}, Module ID: {$ec->module_id}\n";
}

// Get all modules for the program
$allModules = Module::where('program_id', 38)->get();
echo "\nAll modules in program 38:\n";
foreach ($allModules as $module) {
    echo "- Module ID: {$module->modules_id}, Name: {$module->module_name}\n";
}

// Test our filtering logic
$enrolledCourseIds = $enrollmentCourses->pluck('course_id')->toArray();
echo "\nEnrolled course IDs: " . implode(', ', $enrolledCourseIds) . "\n";

// Get the module IDs that contain these courses
$moduleIdsWithEnrolledCourses = \App\Models\Course::whereIn('subject_id', $enrolledCourseIds)
    ->pluck('module_id')
    ->unique()
    ->toArray();

echo "Module IDs containing enrolled courses: " . implode(', ', $moduleIdsWithEnrolledCourses) . "\n";

// Filter modules
$filteredModules = $allModules->filter(function($module) use ($moduleIdsWithEnrolledCourses) {
    return in_array($module->modules_id, $moduleIdsWithEnrolledCourses);
});

echo "\nFiltered modules:\n";
foreach ($filteredModules as $module) {
    echo "- Module ID: {$module->modules_id}, Name: {$module->module_name}\n";
}
