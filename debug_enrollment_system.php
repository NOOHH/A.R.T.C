<?php
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Student;
use App\Models\Program;
use App\Models\Package;
use App\Models\StudentBatch;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Schema;

echo "=== ENROLLMENT SYSTEM DEBUG ===\n\n";

echo "1. CHECKING DATABASE TABLES:\n";
echo "Students table exists: " . (Schema::hasTable('students') ? 'YES' : 'NO') . "\n";
echo "Programs table exists: " . (Schema::hasTable('programs') ? 'YES' : 'NO') . "\n";
echo "Packages table exists: " . (Schema::hasTable('packages') ? 'YES' : 'NO') . "\n";
echo "Student_batches table exists: " . (Schema::hasTable('student_batches') ? 'YES' : 'NO') . "\n";
echo "Courses table exists: " . (Schema::hasTable('courses') ? 'YES' : 'NO') . "\n";
echo "Enrollments table exists: " . (Schema::hasTable('enrollments') ? 'YES' : 'NO') . "\n\n";

echo "2. CHECKING DATA COUNTS:\n";
echo "Total students: " . Student::count() . "\n";
echo "Approved students: " . Student::whereNotNull('date_approved')->count() . "\n";
echo "Non-archived students: " . Student::where('is_archived', false)->count() . "\n";
echo "Total programs: " . Program::count() . "\n";
echo "Active programs: " . Program::where('is_archived', false)->count() . "\n";
echo "Total packages: " . Package::count() . "\n";
echo "Total batches: " . StudentBatch::count() . "\n";
echo "Total courses: " . Course::count() . "\n";
echo "Total enrollments: " . Enrollment::count() . "\n\n";

echo "3. SAMPLE STUDENTS (for enrollment):\n";
$students = Student::where('is_archived', false)
    ->whereNotNull('date_approved')
    ->limit(5)
    ->get(['student_id', 'firstname', 'lastname', 'email']);

foreach ($students as $student) {
    echo "- {$student->student_id}: {$student->firstname} {$student->lastname} ({$student->email})\n";
}

echo "\n4. SAMPLE PROGRAMS:\n";
$programs = Program::where('is_archived', false)->limit(5)->get(['program_id', 'program_name']);
foreach ($programs as $program) {
    echo "- {$program->program_id}: {$program->program_name}\n";
}

echo "\n5. SAMPLE PACKAGES:\n";
$packages = Package::limit(5)->get(['package_id', 'package_name', 'program_id']);
foreach ($packages as $package) {
    echo "- {$package->package_id}: {$package->package_name} (Program: {$package->program_id})\n";
}

echo "\n6. SAMPLE BATCHES:\n";
$batches = StudentBatch::limit(5)->get(['batch_id', 'batch_name', 'start_date']);
foreach ($batches as $batch) {
    echo "- {$batch->batch_id}: {$batch->batch_name} (Start: {$batch->start_date})\n";
}

echo "\n7. SAMPLE COURSES:\n";
$courses = Course::limit(5)->get(['subject_id', 'subject_name']);
foreach ($courses as $course) {
    echo "- {$course->subject_id}: {$course->subject_name}\n";
}

echo "\n8. CHECKING ENROLLMENT RELATIONSHIPS:\n";
try {
    $sampleStudent = Student::with(['enrollments.program', 'enrollments.package'])->first();
    if ($sampleStudent) {
        echo "Sample student: {$sampleStudent->firstname} {$sampleStudent->lastname}\n";
        echo "Enrollments count: " . $sampleStudent->enrollments->count() . "\n";
        foreach ($sampleStudent->enrollments as $enrollment) {
            echo "  - Enrolled in: " . ($enrollment->program->program_name ?? 'Unknown Program') . "\n";
        }
    } else {
        echo "No students found\n";
    }
} catch (Exception $e) {
    echo "Error checking relationships: " . $e->getMessage() . "\n";
}

echo "\n9. CHECKING ADMIN ENROLLMENT MANAGEMENT ROUTE:\n";
try {
    $url = route('admin.enrollments.index');
    echo "Enrollment management URL: {$url}\n";
} catch (Exception $e) {
    echo "Error getting route: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
