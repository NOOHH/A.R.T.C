<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ANNOUNCEMENTS ===\n";
$announcements = App\Models\Announcement::take(3)->get();
foreach ($announcements as $ann) {
    echo "ID: {$ann->announcement_id}, Title: {$ann->title}, Created: {$ann->created_at}, Active: " . ($ann->is_active ? 'Yes' : 'No') . "\n";
}

echo "\n=== ASSIGNMENTS ===\n";
$assignments = App\Models\Assignment::take(3)->get();
foreach ($assignments as $assignment) {
    echo "ID: {$assignment->assignment_id}, Title: {$assignment->title}, Due: {$assignment->due_date}, Active: " . ($assignment->is_active ? 'Yes' : 'No') . "\n";
}

echo "\n=== CLASS MEETINGS ===\n";
$meetings = App\Models\ClassMeeting::take(3)->get();
foreach ($meetings as $meeting) {
    echo "ID: {$meeting->meeting_id}, Title: {$meeting->title}, Date: {$meeting->meeting_date}, Status: {$meeting->status}\n";
}

echo "\n=== STUDENTS ===\n";
$students = App\Models\Student::take(3)->get();
foreach ($students as $student) {
    echo "ID: {$student->student_id}, User ID: {$student->user_id}\n";
}

echo "\n=== SAMPLE STUDENT ENROLLMENTS ===\n";
$student = App\Models\Student::first();
if ($student) {
    echo "Student: {$student->student_id}\n";
    $enrollments = $student->enrollments()->take(3)->get();
    foreach ($enrollments as $enrollment) {
        echo "  Enrollment ID: {$enrollment->enrollment_id}, Program ID: {$enrollment->program_id}, Batch ID: {$enrollment->batch_id}\n";
    }
}
