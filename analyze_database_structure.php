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

echo "=== DATABASE STRUCTURE ANALYSIS ===\n\n";

try {
    // Get all tables
    $tables = DB::select('SHOW TABLES');
    $tableColumn = 'Tables_in_' . env('DB_DATABASE', 'artc');
    
    echo "=== RELEVANT TABLES ===\n";
    $relevantTables = [];
    
    foreach ($tables as $table) {
        $tableName = $table->$tableColumn;
        if (str_contains($tableName, 'completion') || 
            str_contains($tableName, 'progress') || 
            str_contains($tableName, 'enrollment') ||
            str_contains($tableName, 'module') ||
            str_contains($tableName, 'course') ||
            str_contains($tableName, 'student') ||
            str_contains($tableName, 'board')) {
            $relevantTables[] = $tableName;
            echo "Found: {$tableName}\n";
        }
    }
    
    echo "\n=== TABLE DETAILS ===\n";
    
    foreach ($relevantTables as $tableName) {
        try {
            echo "\n--- {$tableName} ---\n";
            
            // Get table structure
            $columns = DB::select("DESCRIBE {$tableName}");
            echo "Columns: ";
            foreach ($columns as $column) {
                echo $column->Field . " ";
            }
            echo "\n";
            
            // Get record count
            $count = DB::table($tableName)->count();
            echo "Records: {$count}\n";
            
            // If has data, show sample
            if ($count > 0) {
                $sample = DB::table($tableName)->first();
                echo "Sample record: " . json_encode($sample) . "\n";
            }
            
        } catch (Exception $e) {
            echo "Error accessing {$tableName}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== STUDENT COMPLETION ANALYSIS ===\n";
    
    // Check for any students
    try {
        $studentCount = DB::table('students')->count();
        echo "Total students: {$studentCount}\n";
        
        if ($studentCount > 0) {
            // Check enrollments
            if (in_array('enrollments', $relevantTables)) {
                $enrollmentCount = DB::table('enrollments')->count();
                echo "Total enrollments: {$enrollmentCount}\n";
                
                if ($enrollmentCount > 0) {
                    // Check for completion indicators
                    $completedStatus = DB::table('enrollments')->where('enrollment_status', 'completed')->count();
                    $highProgress = DB::table('enrollments')->where('progress_percentage', '>=', 90)->count();
                    $certificateIssued = DB::table('enrollments')->where('certificate_issued', 1)->count();
                    
                    echo "Completed status: {$completedStatus}\n";
                    echo "High progress (90%+): {$highProgress}\n";
                    echo "Certificate issued: {$certificateIssued}\n";
                    
                    // Show sample enrollment with high completion
                    $sampleEnrollment = DB::table('enrollments')
                        ->orderBy('progress_percentage', 'desc')
                        ->first();
                    echo "Highest progress enrollment: " . json_encode($sampleEnrollment) . "\n";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "Error analyzing students: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== BOARD PASSERS ANALYSIS ===\n";
    
    try {
        if (in_array('board_passers', $relevantTables)) {
            $passerCount = DB::table('board_passers')->count();
            echo "Total board passers: {$passerCount}\n";
            
            if ($passerCount > 0) {
                $sample = DB::table('board_passers')->first();
                echo "Sample board passer: " . json_encode($sample) . "\n";
                
                // Check for missing data
                $missingNames = DB::table('board_passers')->whereNull('student_name')->orWhere('student_name', '')->count();
                $missingPrograms = DB::table('board_passers')->whereNull('program')->orWhere('program', '')->count();
                
                echo "Missing student names: {$missingNames}\n";
                echo "Missing programs: {$missingPrograms}\n";
            }
        }
    } catch (Exception $e) {
        echo "Error analyzing board passers: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";

?>
