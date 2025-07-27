<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== A.R.T.C Database Check ===\n\n";

try {
    // Check programs
    $programs = \App\Models\Program::select('program_id', 'program_name', 'program_description', 'is_active', 'is_archived')->get();
    echo "Programs in database: " . $programs->count() . "\n";
    
    foreach ($programs as $program) {
        echo "- ID: {$program->program_id}, Name: '{$program->program_name}', Active: " . 
             ($program->is_active ? 'Yes' : 'No') . ", Archived: " . 
             ($program->is_archived ? 'Yes' : 'No') . "\n";
    }
    
    echo "\n";
    
    // Check students and enrollments
    $students = \App\Models\Student::with('enrollments')->get();
    echo "Students in database: " . $students->count() . "\n";
    
    foreach ($students->take(3) as $student) {
        echo "- Student ID: {$student->student_id}, User ID: {$student->user_id}, Enrollments: " . 
             $student->enrollments->count() . "\n";
    }
    
    echo "\n";
    
    // Check if there are any programs containing 'nursing'
    $nursingPrograms = \App\Models\Program::where('program_name', 'like', '%nursing%')
                                         ->orWhere('program_description', 'like', '%nursing%')
                                         ->get();
    echo "Programs containing 'nursing': " . $nursingPrograms->count() . "\n";
    
    foreach ($nursingPrograms as $program) {
        echo "- {$program->program_name}: {$program->program_description}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
