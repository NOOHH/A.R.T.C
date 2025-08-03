<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Student;
use App\Models\Program;
use App\Models\Module;
use App\Models\Course;
use App\Models\Package;
use App\Models\StudentBatch;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

echo "=== COMPREHENSIVE ENROLLMENT SYSTEM TEST ===\n\n";

// 1. Check basic data availability
echo "1. CHECKING DATA AVAILABILITY:\n";
$studentsCount = Student::where('is_archived', false)->whereNotNull('date_approved')->count();
$programsCount = Program::where('is_archived', false)->count();
$modulesCount = Module::where('is_archived', false)->count();
$coursesCount = Course::where('is_archived', false)->count();
$batchesCount = StudentBatch::count();

echo "- Students (approved, non-archived): {$studentsCount}\n";
echo "- Programs (non-archived): {$programsCount}\n";
echo "- Modules (non-archived): {$modulesCount}\n";
echo "- Courses (non-archived): {$coursesCount}\n";
echo "- Batches: {$batchesCount}\n\n";

// 2. Test module-program relationships
echo "2. TESTING MODULE-PROGRAM RELATIONSHIPS:\n";
$programs = Program::where('is_archived', false)->limit(2)->get();
foreach ($programs as $program) {
    $modules = Module::where('program_id', $program->program_id)
        ->where('is_archived', false)
        ->get(['modules_id', 'module_name']);
    echo "Program {$program->program_id} ({$program->program_name}): {$modules->count()} modules\n";
    foreach ($modules->take(2) as $module) {
        echo "  - Module {$module->modules_id}: {$module->module_name}\n";
    }
}
echo "\n";

// 3. Test course-module relationships
echo "3. TESTING COURSE-MODULE RELATIONSHIPS:\n";
$modules = Module::where('is_archived', false)->limit(2)->get(['modules_id', 'module_name']);
foreach ($modules as $module) {
    $courses = Course::where('module_id', $module->modules_id)
        ->where('is_archived', false)
        ->get(['subject_id', 'subject_name']);
    echo "Module {$module->modules_id} ({$module->module_name}): {$courses->count()} courses\n";
    foreach ($courses->take(2) as $course) {
        echo "  - Course {$course->subject_id}: {$course->subject_name}\n";
    }
}
echo "\n";

// 4. Check enrollment_courses table
echo "4. CHECKING ENROLLMENT_COURSES TABLE:\n";
$enrollmentCoursesCount = DB::table('enrollment_courses')->count();
echo "- Total enrollment_courses records: {$enrollmentCoursesCount}\n";

if ($enrollmentCoursesCount > 0) {
    $sampleRecords = DB::table('enrollment_courses')->limit(3)->get();
    echo "- Sample records:\n";
    foreach ($sampleRecords as $record) {
        echo "  ID: {$record->id}, Enrollment: {$record->enrollment_id}, Course: {$record->course_id}, Module: {$record->module_id}\n";
    }
}
echo "\n";

// 5. Test API route simulation
echo "5. TESTING API LOGIC:\n";
if ($programs->count() > 0) {
    $testProgram = $programs->first();
    echo "Testing modules for program {$testProgram->program_id}:\n";
    
    $modules = Module::where('program_id', $testProgram->program_id)
        ->where('is_archived', false)
        ->orderBy('module_name')
        ->get(['modules_id', 'module_name']);
        
    echo "Found {$modules->count()} modules:\n";
    foreach ($modules->take(3) as $module) {
        echo "  - {$module->modules_id}: {$module->module_name}\n";
        
        // Test courses for this module
        $courses = Course::where('module_id', $module->modules_id)
            ->where('is_archived', false)
            ->orderBy('subject_name')
            ->get(['subject_id', 'subject_name']);
        echo "    Courses: {$courses->count()}\n";
        foreach ($courses->take(2) as $course) {
            echo "      - {$course->subject_id}: {$course->subject_name}\n";
        }
    }
}
echo "\n";

// 6. Test enrollment creation simulation
echo "6. TESTING ENROLLMENT CREATION:\n";
$student = Student::where('is_archived', false)->whereNotNull('date_approved')->first();
$program = Program::where('is_archived', false)->first();
$package = Package::where('program_id', $program->program_id)->first();
$batch = StudentBatch::first();

if ($student && $program && $package && $batch) {
    echo "Test data available:\n";
    echo "- Student: {$student->firstname} {$student->lastname} ({$student->student_id})\n";
    echo "- Program: {$program->program_name} ({$program->program_id})\n";
    echo "- Package: {$package->package_name} ({$package->package_id})\n";
    echo "- Batch: {$batch->batch_name} ({$batch->batch_id})\n";
    
    // Check if student is already enrolled
    $existingEnrollment = Enrollment::where([
        'student_id' => $student->student_id,
        'program_id' => $program->program_id
    ])->first();
    
    if ($existingEnrollment) {
        echo "- Status: Student already enrolled (ID: {$existingEnrollment->enrollment_id})\n";
        
        // Check modular enrollments
        $modularEnrollments = DB::table('enrollment_courses')
            ->where('enrollment_id', $existingEnrollment->enrollment_id)
            ->get();
        echo "- Modular courses enrolled: {$modularEnrollments->count()}\n";
    } else {
        echo "- Status: Student can be enrolled\n";
    }
} else {
    echo "Missing required data for enrollment test\n";
}

echo "\n=== TEST COMPLETE ===\n";
