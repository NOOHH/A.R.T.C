<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Available content items with assignments:\n";
$assignments = \App\Models\ContentItem::where('content_type', 'assignment')->whereNotNull('due_date')->get();
foreach ($assignments as $assignment) {
    echo "- ID: {$assignment->id} | Course ID: {$assignment->course_id} | Title: {$assignment->content_title} | Due: {$assignment->due_date}\n";
}

echo "\nCourse 33 details:\n";
$course = \App\Models\Course::find(33);
if ($course) {
    echo "Course Name: {$course->subject_name} | Module ID: {$course->module_id}\n";
    $module = \App\Models\Module::find($course->module_id);
    if ($module) {
        echo "Module Name: {$module->module_name} | Program ID: {$module->program_id}\n";
    }
} else {
    echo "Course 33 not found\n";
}

echo "\nAll students and their enrollments:\n";
$students = \App\Models\Student::with('enrollments')->get();
foreach ($students as $student) {
    echo "Student ID: {$student->student_id} | User ID: {$student->user_id}\n";
    foreach ($student->enrollments as $enrollment) {
        echo "  - Program ID: {$enrollment->program_id} | Status: {$enrollment->enrollment_status} | Payment: {$enrollment->payment_status}\n";
    }
}
