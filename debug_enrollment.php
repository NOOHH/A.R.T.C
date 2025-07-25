<?php

use Illuminate\Support\Facades\DB;

// Debug script to check modular enrollment data
$enrollmentId = 166;

echo "=== DEBUGGING ENROLLMENT $enrollmentId ===\n\n";

// Get enrollment details
$enrollment = DB::table('enrollments')
    ->where('enrollment_id', $enrollmentId)
    ->first();

if (!$enrollment) {
    echo "Enrollment not found!\n";
    exit;
}

echo "Enrollment Details:\n";
echo "- ID: {$enrollment->enrollment_id}\n";
echo "- User ID: {$enrollment->user_id}\n";
echo "- Program ID: {$enrollment->program_id}\n";
echo "- Enrollment Type: {$enrollment->enrollment_type}\n";
echo "- Status: {$enrollment->enrollment_status}\n\n";

// Get registration data
$registration = DB::table('registrations')
    ->where('user_id', $enrollment->user_id)
    ->where('program_id', $enrollment->program_id)
    ->where('enrollment_type', 'Modular')
    ->first();

if (!$registration) {
    echo "No registration found for this enrollment!\n";
} else {
    echo "Registration Details:\n";
    echo "- Registration ID: {$registration->registration_id}\n";
    echo "- Selected Modules: {$registration->selected_modules}\n";
    
    if ($registration->selected_modules) {
        $selectedModules = json_decode($registration->selected_modules, true);
        echo "- Parsed Modules: " . print_r($selectedModules, true) . "\n";
    }
}

echo "\n";

// Get enrollment courses
$enrollmentCourses = DB::table('enrollment_courses')
    ->where('enrollment_id', $enrollmentId)
    ->get();

echo "Enrollment Courses:\n";
if ($enrollmentCourses->isEmpty()) {
    echo "- No enrollment courses found\n";
} else {
    foreach ($enrollmentCourses as $ec) {
        echo "- Course ID: {$ec->course_id}, Module ID: {$ec->module_id}, Active: " . ($ec->is_active ? 'Yes' : 'No') . "\n";
    }
}

echo "\n=== END DEBUG ===\n";
