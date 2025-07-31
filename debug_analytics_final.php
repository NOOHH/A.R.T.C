<?php

// Set up Laravel environment
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/test';

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Create a fake request
$request = Illuminate\Http\Request::create('/test', 'GET');
$app->instance('request', $request);

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== ANALYTICS DATABASE DEBUG ===\n\n";

try {
    // 1. Check if board_passers table exists and what's in it
    echo "1. BOARD PASSERS TABLE:\n";
    try {
        $passersCount = DB::table('board_passers')->count();
        echo "   Records count: {$passersCount}\n";
        
        if ($passersCount > 0) {
            $sample = DB::table('board_passers')->first();
            echo "   Sample record: " . json_encode($sample, JSON_PRETTY_PRINT) . "\n";
            
            // Check for null values
            $nullStudentName = DB::table('board_passers')->whereNull('student_name')->count();
            $nullProgram = DB::table('board_passers')->whereNull('program')->count();
            echo "   Records with null student_name: {$nullStudentName}\n";
            echo "   Records with null program: {$nullProgram}\n";
        }
    } catch (Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n";
    }
    
    // 2. Check students count
    echo "\n2. STUDENTS COUNT:\n";
    try {
        $studentsCount = DB::table('students')->count();
        echo "   Total students: {$studentsCount}\n";
        
        $usersStudentCount = DB::table('users')->where('role', 'student')->count();
        echo "   Users with role 'student': {$usersStudentCount}\n";
        
        if ($studentsCount > 0) {
            $sampleStudent = DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->select('students.student_id', 'users.user_firstname', 'users.user_lastname', 'users.role')
                ->first();
            echo "   Sample student: " . json_encode($sampleStudent) . "\n";
        }
    } catch (Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n";
    }
    
    // 3. Check enrollments and completions
    echo "\n3. ENROLLMENTS AND COMPLETIONS:\n";
    try {
        $enrollmentsCount = DB::table('enrollments')->count();
        echo "   Total enrollments: {$enrollmentsCount}\n";
        
        if ($enrollmentsCount > 0) {
            $completedCount = DB::table('enrollments')
                ->where(function($q) {
                    $q->where('enrollment_status', 'completed')
                      ->orWhere('progress_percentage', '>=', 90)
                      ->orWhere('certificate_issued', 1);
                })->count();
            echo "   Completed/high progress enrollments: {$completedCount}\n";
            
            $sampleEnrollment = DB::table('enrollments')
                ->join('students', 'enrollments.student_id', '=', 'students.student_id')
                ->join('users', 'students.user_id', '=', 'users.user_id')
                ->select([
                    'users.user_firstname',
                    'users.user_lastname',
                    'enrollments.enrollment_status',
                    'enrollments.progress_percentage',
                    'enrollments.certificate_issued'
                ])
                ->first();
            echo "   Sample enrollment: " . json_encode($sampleEnrollment) . "\n";
        }
    } catch (Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n";
    }
    
    // 4. Test the analytics methods directly
    echo "\n4. TESTING ANALYTICS CONTROLLER:\n";
    try {
        $controller = new App\Http\Controllers\AdminAnalyticsController();
        
        // Get filters (empty for now)
        $filters = [];
        
        // Test metrics
        $reflection = new ReflectionClass($controller);
        
        // Test buildStudentsQuery
        $buildMethod = $reflection->getMethod('buildStudentsQuery');
        $buildMethod->setAccessible(true);
        $studentsQuery = $buildMethod->invoke($controller, $filters);
        $studentCount = $studentsQuery->count();
        echo "   Analytics student count: {$studentCount}\n";
        
        // Test getRecentlyCompleted
        $completedMethod = $reflection->getMethod('getRecentlyCompleted');
        $completedMethod->setAccessible(true);
        $recentlyCompleted = $completedMethod->invoke($controller, $filters);
        echo "   Recently completed count: " . count($recentlyCompleted) . "\n";
        if (!empty($recentlyCompleted)) {
            echo "   Sample completed: " . json_encode($recentlyCompleted[0]) . "\n";
        }
        
        // Test getBoardPassers
        $passersMethod = $reflection->getMethod('getBoardPassers');
        $passersMethod->setAccessible(true);
        $boardPassers = $passersMethod->invoke($controller, $filters);
        echo "   Board passers count: " . count($boardPassers) . "\n";
        if (!empty($boardPassers)) {
            echo "   Sample board passer: " . json_encode($boardPassers[0]) . "\n";
        }
        
    } catch (Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";

?>
