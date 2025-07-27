<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fixing enrollment_courses records for course 33...\n";

// Get the module_id for course 33
$course = \App\Models\Course::find(33);
if (!$course) {
    echo "Course 33 not found!\n";
    exit;
}

$moduleId = $course->module_id;
echo "Course 33 belongs to module {$moduleId}\n";

// Find all enrollments for Program 39 (which contains course 33)
$program39Enrollments = \App\Models\Enrollment::where('program_id', 39)
    ->where('enrollment_status', 'approved')
    ->get();

echo "Found " . $program39Enrollments->count() . " approved enrollments for Program 39\n";

foreach ($program39Enrollments as $enrollment) {
    echo "Checking enrollment {$enrollment->enrollment_id} for student {$enrollment->student_id}...\n";
    
    // Check if enrollment_courses record exists for course 33
    $existingRecord = \App\Models\EnrollmentCourse::where('enrollment_id', $enrollment->enrollment_id)
        ->where('course_id', 33)
        ->first();
    
    if (!$existingRecord) {
        echo "  - Creating enrollment_courses record for course 33\n";
        \App\Models\EnrollmentCourse::create([
            'enrollment_id' => $enrollment->enrollment_id,
            'course_id' => 33,
            'module_id' => $moduleId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    } else {
        echo "  - Record already exists, making sure it's active\n";
        $existingRecord->update(['is_active' => true]);
    }
}

echo "\nRe-checking enrolledCourseIds after fix:\n";
$students = \App\Models\Student::all();
foreach ($students as $student) {
    echo "Student {$student->student_id}:\n";
    $enrolledCourseIds = \App\Models\EnrollmentCourse::whereHas('enrollment', function($q) use ($student) {
        $q->where('student_id', $student->student_id);
    })->where('is_active', true)->pluck('course_id')->toArray();
    
    echo "  Enrolled course IDs: " . implode(', ', $enrolledCourseIds) . "\n";
    echo "  Contains course 33? " . (in_array(33, $enrolledCourseIds) ? 'YES' : 'NO') . "\n";
}

echo "\nDone!\n";
