<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking enrollment_courses table:\n";
$enrollmentCourses = \App\Models\EnrollmentCourse::all();
echo "Total enrollment_courses records: " . $enrollmentCourses->count() . "\n";

foreach ($enrollmentCourses as $ec) {
    echo "- Enrollment ID: {$ec->enrollment_id} | Course ID: {$ec->course_id} | Active: " . ($ec->is_active ? 'Yes' : 'No') . "\n";
}

echo "\nChecking what enrolledCourseIds would be for each student:\n";
$students = \App\Models\Student::all();
foreach ($students as $student) {
    echo "Student {$student->student_id}:\n";
    $enrolledCourseIds = \App\Models\EnrollmentCourse::whereHas('enrollment', function($q) use ($student) {
        $q->where('student_id', $student->student_id);
    })->where('is_active', true)->pluck('course_id')->toArray();
    
    echo "  Enrolled course IDs: " . implode(', ', $enrolledCourseIds) . "\n";
    echo "  Contains course 33? " . (in_array(33, $enrolledCourseIds) ? 'YES' : 'NO') . "\n";
}

echo "\nAll enrollments:\n";
$enrollments = \App\Models\Enrollment::all();
foreach ($enrollments as $enrollment) {
    echo "- ID: {$enrollment->enrollment_id} | Student: {$enrollment->student_id} | Program: {$enrollment->program_id} | Status: {$enrollment->enrollment_status}\n";
}
