<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing API logic for enrolled courses:\n";

$userId = 174;
$enrolledCourseIds = \App\Models\EnrollmentCourse::whereHas('enrollment', function($query) use ($userId) {
    $query->where('user_id', $userId)
          ->where('enrollment_status', '!=', 'rejected');
})->where('is_active', true)
  ->pluck('course_id')
  ->toArray();

echo "Enrolled Course IDs: " . implode(', ', $enrolledCourseIds) . "\n\n";

$programs = \App\Models\Program::with(['modules.courses.contentItems'])
    ->where('is_archived', false)
    ->get();

foreach($programs as $program) {
    if($program->program_name === 'Civil Engineering') {
        echo "Civil Engineering Program:\n";
        foreach($program->modules as $module) {
            echo "  Module: {$module->module_name}\n";
            foreach($module->courses as $course) {
                $isAlreadyEnrolled = in_array($course->subject_id, $enrolledCourseIds);
                echo "    Course: {$course->subject_name} (Subject ID: {$course->subject_id}) - " . 
                     ($isAlreadyEnrolled ? 'ALREADY ENROLLED' : 'Available') . "\n";
            }
        }
    }
}

echo "\nTesting exact API output format:\n";
foreach($programs as $program) {
    if($program->program_name === 'Civil Engineering') {
        foreach($program->modules as $module) {
            echo "Module: {$module->module_name} (ID: {$module->modules_id})\n";
            foreach($module->courses as $course) {
                $isAlreadyEnrolled = in_array($course->subject_id, $enrolledCourseIds);
                
                $courseData = [
                    'course_id' => $course->subject_id,
                    'course_name' => $course->subject_name,
                    'description' => $course->subject_description,
                    'content_items_count' => $course->contentItems->count(),
                    'already_enrolled' => $isAlreadyEnrolled,
                ];
                
                echo "  Course JSON: " . json_encode($courseData) . "\n";
            }
        }
    }
}
