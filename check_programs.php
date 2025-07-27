<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Available Programs:\n";
$programs = \App\Models\Program::where('is_archived', false)->get();
foreach($programs as $program) {
    echo "Program: {$program->program_name} (ID: {$program->program_id})\n";
}

echo "\nTesting enrolled courses for User 174:\n";
$userId = 174;
$enrolledCourseIds = \App\Models\EnrollmentCourse::whereHas('enrollment', function($query) use ($userId) {
    $query->where('user_id', $userId)
          ->where('enrollment_status', '!=', 'rejected');
})->where('is_active', true)
  ->pluck('course_id')
  ->toArray();

echo "Enrolled Course IDs: " . implode(', ', $enrolledCourseIds) . "\n";

// Find the Civil Engineer program (note: might be "Civil Engineer" not "Civil Engineering")
$civilProgram = $programs->where('program_name', 'Civil Engineer')->first();
if ($civilProgram) {
    echo "\nFound Civil Engineer Program (ID: {$civilProgram->program_id})\n";
    
    // Load modules and courses
    $civilProgram->load(['modules.courses.contentItems']);
    
    foreach($civilProgram->modules as $module) {
        echo "  Module: {$module->module_name} (ID: {$module->modules_id})\n";
        foreach($module->courses as $course) {
            $isAlreadyEnrolled = in_array($course->subject_id, $enrolledCourseIds);
            echo "    Course: {$course->subject_name} (ID: {$course->subject_id}) - " . 
                 ($isAlreadyEnrolled ? 'ALREADY ENROLLED' : 'Available') . "\n";
        }
    }
} else {
    echo "\nCivil Engineer program not found!\n";
}
