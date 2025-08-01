<?php

// Test script to check if quiz deadlines are being properly added to the dashboard

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set test session data
session(['user_id' => 41, 'user_role' => 'student']);

try {
    // Get a student record
    $student = \App\Models\Student::where('user_id', 41)->first();
    
    if (!$student) {
        // Try to find any student
        $student = \App\Models\Student::first();
        if (!$student) {
            echo "No students found in database\n";
            exit(1);
        }
        echo "Using student with user_id: {$student->user_id}\n";
    }
    
    echo "Found student: {$student->student_id}\n";
    
    // Get enrolled courses (simplified approach)
    $enrollments = $student->enrollments()->get();
    $enrolledCourseIds = $enrollments->pluck('course_id')->filter()->unique();
    
    echo "Enrolled course IDs: " . $enrolledCourseIds->implode(', ') . "\n";
    
    // Also get program-based course IDs
    $enrolledProgramIds = $enrollments->pluck('program_id')->filter()->unique();
    echo "Enrolled program IDs: " . $enrolledProgramIds->implode(', ') . "\n";
    
    if ($enrolledProgramIds->isNotEmpty()) {
        $programCourseIds = \Illuminate\Support\Facades\DB::table('courses')
            ->whereIn('module_id', function($query) use ($enrolledProgramIds) {
                $query->select('modules_id')
                      ->from('modules')
                      ->whereIn('program_id', $enrolledProgramIds);
            })
            ->pluck('subject_id');
        
        $allCourseIds = $enrolledCourseIds->merge($programCourseIds)->unique();
        echo "All course IDs (including program courses): " . $allCourseIds->implode(', ') . "\n";
    } else {
        $allCourseIds = $enrolledCourseIds;
    }
    
    // Check for quiz content items
    $quizzes = \App\Models\ContentItem::where('content_type', 'quiz')
        ->whereNotNull('due_date')
        ->whereIn('course_id', $allCourseIds)
        ->get();
    
    echo "Found " . $quizzes->count() . " quizzes with due dates:\n";
    
    foreach ($quizzes as $quiz) {
        echo "- Quiz: {$quiz->content_title}, Due: {$quiz->due_date}, Course ID: {$quiz->course_id}\n";
    }
    
    // Check if any assignments exist for comparison
    $assignments = \App\Models\ContentItem::where('content_type', 'assignment')
        ->whereNotNull('due_date')
        ->whereIn('course_id', $allCourseIds)
        ->get();
    
    echo "\nFound " . $assignments->count() . " assignments with due dates:\n";
    
    foreach ($assignments as $assignment) {
        echo "- Assignment: {$assignment->content_title}, Due: {$assignment->due_date}, Course ID: {$assignment->course_id}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
