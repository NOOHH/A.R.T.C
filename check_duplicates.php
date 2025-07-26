<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking enrollment courses:\n";

$enrollmentCourses = \App\Models\EnrollmentCourse::with(['enrollment', 'course'])->get();

foreach($enrollmentCourses as $ec) {
    echo "Enrollment ID: " . $ec->enrollment_id . 
         ", Course ID: " . $ec->course_id . 
         ", Course Name: " . ($ec->course ? $ec->course->subject_name : 'N/A') . 
         ", User ID: " . ($ec->enrollment ? $ec->enrollment->user_id : 'N/A') . 
         ", Active: " . ($ec->is_active ? 'Yes' : 'No') . "\n";
}

echo "\nChecking for duplicates:\n";

// Group by user_id and course_id to find duplicates
$duplicates = \App\Models\EnrollmentCourse::with(['enrollment', 'course'])
    ->whereHas('enrollment', function($query) {
        $query->where('enrollment_status', '!=', 'rejected');
    })
    ->where('is_active', true)
    ->get()
    ->groupBy(function($item) {
        return $item->enrollment->user_id . '_' . $item->course_id;
    })
    ->filter(function($group) {
        return $group->count() > 1;
    });

foreach($duplicates as $key => $group) {
    echo "DUPLICATE FOUND for key $key:\n";
    foreach($group as $duplicate) {
        echo "  - Enrollment ID: " . $duplicate->enrollment_id . 
             ", User ID: " . $duplicate->enrollment->user_id . 
             ", Course: " . ($duplicate->course ? $duplicate->course->subject_name : 'N/A') . "\n";
    }
}

if($duplicates->isEmpty()) {
    echo "No duplicates found.\n";
}
