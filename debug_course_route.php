<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Set up session data (simulating logged-in student)
session([
    'user_id' => 1,
    'user_role' => 'student',
    'user_name' => 'Test Student',
    'user_firstname' => 'Test',
    'user_lastname' => 'Student'
]);

try {
    // Test the course method with program ID 40 (Nursing)
    $controller = new \App\Http\Controllers\StudentDashboardController();
    
    echo "Testing course method with courseId=40 (Nursing Program)\n";
    
    // Check if program exists
    $program = \App\Models\Program::find(40);
    if ($program) {
        echo "Program found: {$program->program_name}\n";
        echo "Program ID: {$program->program_id}\n";
    } else {
        echo "Program with ID 40 not found!\n";
    }
    
    // Test with different program IDs from our data
    foreach ([40, 41] as $programId) {
        echo "\n--- Testing Program ID: $programId ---\n";
        $program = \App\Models\Program::find($programId);
        if ($program) {
            echo "Program: {$program->program_name}\n";
            
            // Check student enrollment
            $student = \App\Models\Student::where('user_id', session('user_id'))->first();
            if ($student) {
                $enrollment = \App\Models\Enrollment::where('student_id', $student->student_id)
                    ->where('program_id', $programId)
                    ->first();
                
                if ($enrollment) {
                    echo "Student is enrolled: Yes\n";
                    echo "Enrollment status: {$enrollment->enrollment_status}\n";
                } else {
                    echo "Student is enrolled: No\n";
                }
            }
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
