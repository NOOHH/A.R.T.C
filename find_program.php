<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Program;
use App\Models\Student;
use App\Models\Enrollment;

echo "Finding Mechanical Engineer program...\n";

$programs = Program::where('program_name', 'like', '%Mechanical%')->get();
foreach ($programs as $program) {
    echo "Program ID: {$program->program_id} - Name: {$program->program_name}\n";
}

echo "\nChecking student 2025-07-00001 enrollments...\n";
$student = Student::where('student_id', '2025-07-00001')->first();
if ($student) {
    $enrollments = Enrollment::where('user_id', $student->user_id)->with('program')->get();
    foreach ($enrollments as $enrollment) {
        echo "Enrollment ID: {$enrollment->enrollment_id} - Program: {$enrollment->program->program_name} (ID: {$enrollment->program_id}) - Type: {$enrollment->enrollment_type}\n";
    }
}
