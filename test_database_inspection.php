<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== COMPREHENSIVE DATABASE INSPECTION ===\n";

try {
    echo "1. CHECKING ALL TABLES AND THEIR RECORD COUNTS...\n";
    
    // Get all tables in the database
    $tables = DB::select('SHOW TABLES');
    $databaseName = DB::connection()->getDatabaseName();
    
    foreach ($tables as $table) {
        $tableName = $table->{'Tables_in_' . $databaseName};
        try {
            $count = DB::table($tableName)->count();
            echo "   📊 Table '$tableName': $count records\n";
            
            // For key tables, show sample data
            if (in_array($tableName, ['students', 'enrollments', 'programs', 'batches', 'payments', 'users']) && $count > 0) {
                echo "      Sample data:\n";
                $sample = DB::table($tableName)->limit(2)->get();
                foreach ($sample as $row) {
                    $data = json_encode($row, JSON_PRETTY_PRINT);
                    echo "      " . substr($data, 0, 100) . "...\n";
                }
            }
        } catch (Exception $e) {
            echo "   ❌ Error checking table '$tableName': " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n2. CHECKING SPECIFIC ANALYTICS DATA SOURCES...\n";
    
    // Check enrollments table specifically
    echo "   📋 ENROLLMENTS TABLE ANALYSIS:\n";
    $enrollments = DB::table('enrollments')->get();
    echo "      Total enrollments: " . $enrollments->count() . "\n";
    
    if ($enrollments->count() > 0) {
        foreach ($enrollments as $enrollment) {
            echo "      - Student: {$enrollment->student_id}, Program: {$enrollment->program_id}, Status: " . ($enrollment->enrollment_status ?? 'N/A') . "\n";
        }
    }
    
    // Check programs table
    echo "\n   🎓 PROGRAMS TABLE ANALYSIS:\n";
    $programs = DB::table('programs')->get();
    echo "      Total programs: " . $programs->count() . "\n";
    
    if ($programs->count() > 0) {
        foreach ($programs as $program) {
            echo "      - ID: {$program->program_id}, Name: " . ($program->program_name ?? 'N/A') . "\n";
        }
    }
    
    // Check batches table
    echo "\n   📚 BATCHES TABLE ANALYSIS:\n";
    if (DB::getSchemaBuilder()->hasTable('batches')) {
        $batches = DB::table('batches')->get();
        echo "      Total batches: " . $batches->count() . "\n";
        
        if ($batches->count() > 0) {
            foreach ($batches as $batch) {
                echo "      - ID: {$batch->batch_id}, Name: " . ($batch->batch_name ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "      ❌ Batches table does not exist\n";
    }
    
    // Check students table
    echo "\n   👥 STUDENTS TABLE ANALYSIS:\n";
    $students = DB::table('students')->get();
    echo "      Total students: " . $students->count() . "\n";
    
    if ($students->count() > 0) {
        foreach ($students as $student) {
            echo "      - ID: {$student->student_id}, User ID: {$student->user_id}\n";
        }
    }
    
    echo "\n3. TESTING ANALYTICS CONTROLLER DATA METHODS...\n";
    
    // Test the actual controller methods that provide analytics data
    try {
        $controller = new App\Http\Controllers\AdminAnalyticsController();
        
        // Use reflection to call private methods
        $reflection = new ReflectionClass($controller);
        
        // Test getBatchPerformance
        if ($reflection->hasMethod('getBatchPerformance')) {
            $method = $reflection->getMethod('getBatchPerformance');
            $method->setAccessible(true);
            $batchData = $method->invoke($controller, []);
            echo "   📊 getBatchPerformance returned " . count($batchData) . " batches:\n";
            foreach ($batchData as $batch) {
                echo "      - " . json_encode($batch) . "\n";
            }
        }
        
        // Test getData method
        echo "\n   🔍 Testing main getData method...\n";
        if (method_exists($controller, 'getData')) {
            try {
                // Create a mock request
                $request = new Illuminate\Http\Request();
                $response = $controller->getData($request);
                
                if ($response instanceof Illuminate\Http\JsonResponse) {
                    $data = $response->getData(true);
                    echo "      ✅ getData method executed successfully\n";
                    
                    if (isset($data['metrics'])) {
                        echo "      📊 Metrics found:\n";
                        foreach ($data['metrics'] as $key => $value) {
                            echo "         - $key: $value\n";
                        }
                    }
                    
                    if (isset($data['tables'])) {
                        echo "      📋 Tables data:\n";
                        foreach ($data['tables'] as $key => $value) {
                            echo "         - $key: " . (is_array($value) ? count($value) . " items" : $value) . "\n";
                        }
                    }
                }
            } catch (Exception $e) {
                echo "      ❌ getData method failed: " . $e->getMessage() . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Controller testing failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n4. CHECKING FOR CACHED DATA OR CONFIG ISSUES...\n";
    
    // Check if there are any cache files
    $cacheDirectories = [
        'bootstrap/cache',
        'storage/framework/cache',
        'storage/framework/views'
    ];
    
    foreach ($cacheDirectories as $dir) {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            echo "   📁 Cache directory '$dir': " . count($files) . " files\n";
        }
    }
    
    echo "\n=== DATABASE INSPECTION COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
