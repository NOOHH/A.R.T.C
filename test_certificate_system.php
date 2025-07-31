<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== CERTIFICATE SYSTEM TESTING ===" . PHP_EOL;

try {
    echo "1. Checking enrollments table structure..." . PHP_EOL;
    $enrollmentColumns = DB::select('DESCRIBE enrollments');
    foreach($enrollmentColumns as $col) {
        echo "   - {$col->Field} ({$col->Type})" . PHP_EOL;
    }
    
    echo PHP_EOL . "2. Checking students with 100% progress..." . PHP_EOL;
    $completedStudents = DB::table('enrollments')
        ->join('students', 'enrollments.student_id', '=', 'students.student_id')
        ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
        ->where(function($query) {
            $query->where('enrollments.enrollment_status', 'completed')
                  ->orWhere('enrollments.progress_percentage', '>=', 100);
        })
        ->select('students.student_id', 'students.firstname', 'students.lastname', 
                'programs.program_name', 'enrollments.progress_percentage', 
                'enrollments.enrollment_status', 'enrollments.start_date', 
                'enrollments.completion_date')
        ->take(10)
        ->get();
    
    if ($completedStudents->isEmpty()) {
        echo "   No students with 100% progress found." . PHP_EOL;
    } else {
        foreach($completedStudents as $student) {
            echo "   - {$student->firstname} {$student->lastname} ({$student->program_name}) - Progress: {$student->progress_percentage}% - Status: {$student->enrollment_status}" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "3. Checking if certificates table exists..." . PHP_EOL;
    try {
        $certificateColumns = DB::select('DESCRIBE certificates');
        echo "   Certificates table exists with columns:" . PHP_EOL;
        foreach($certificateColumns as $col) {
            echo "   - {$col->Field} ({$col->Type})" . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "   Certificates table does not exist. Error: " . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL . "4. Checking course completions..." . PHP_EOL;
    try {
        $courseCompletions = DB::select('DESCRIBE course_completions');
        echo "   Course completions table exists with columns:" . PHP_EOL;
        foreach($courseCompletions as $col) {
            echo "   - {$col->Field} ({$col->Type})" . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "   Course completions table does not exist. Error: " . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL . "5. Checking module completions..." . PHP_EOL;
    try {
        $moduleCompletions = DB::select('DESCRIBE module_completions');
        echo "   Module completions table exists with columns:" . PHP_EOL;
        foreach($moduleCompletions as $col) {
            echo "   - {$col->Field} ({$col->Type})" . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "   Module completions table does not exist. Error: " . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL . "6. Testing progress calculation..." . PHP_EOL;
    $sampleStudent = DB::table('students')->first();
    if ($sampleStudent) {
        echo "   Sample student: {$sampleStudent->firstname} {$sampleStudent->lastname}" . PHP_EOL;
        
        // Check enrollments for this student
        $studentEnrollments = DB::table('enrollments')
            ->where('student_id', $sampleStudent->student_id)
            ->get();
        
        echo "   Student has " . count($studentEnrollments) . " enrollment(s)" . PHP_EOL;
        foreach($studentEnrollments as $enrollment) {
            echo "   - Program ID: {$enrollment->program_id}, Status: {$enrollment->enrollment_status}, Progress: {$enrollment->progress_percentage}%" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "✅ Certificate system testing completed successfully!" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}
