#!/usr/bin/env php
<?php
/*
 * Complete Test for File Upload and Storage in Database
 * Tests OCR system with sample5.png and verifies database storage
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\OcrService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

echo "=== COMPLETE FILE UPLOAD AND DATABASE STORAGE TEST ===\n";

// Test 1: Check sample5.png exists
$samplePath = base_path('resources/images/OCR Images/sample5.png');
if (!file_exists($samplePath)) {
    echo "❌ Sample file not found at: $samplePath\n";
    exit(1);
}
echo "✅ Sample file found: $samplePath\n";

// Test 2: Test OCR service
$ocrService = new OcrService();

try {
    $extractedText = $ocrService->extractText($samplePath);
    echo "✅ OCR extraction successful\n";
    echo "📄 Extracted text length: " . strlen($extractedText) . " characters\n";
    echo "📄 Text preview: " . substr($extractedText, 0, 200) . "...\n";
} catch (Exception $e) {
    echo "❌ OCR extraction failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Test program suggestions
try {
    $suggestions = $ocrService->suggestPrograms($extractedText);
    echo "✅ Program suggestions generated: " . count($suggestions) . " found\n";
    foreach ($suggestions as $suggestion) {
        echo "   🎓 " . ($suggestion['program_name'] ?? 'Unknown') . " (Score: " . ($suggestion['score'] ?? 0) . ")\n";
    }
} catch (Exception $e) {
    echo "❌ Program suggestions failed: " . $e->getMessage() . "\n";
}

// Test 4: Test name validation
$testNames = [
    ['first' => 'Juanita', 'last' => 'Reño'],
    ['first' => 'John', 'last' => 'Doe'],
    ['first' => 'Maria', 'last' => 'Santos']
];

foreach ($testNames as $name) {
    try {
        $isValid = $ocrService->validateUserName($extractedText, $name['first'], $name['last']);
        echo ($isValid ? "✅" : "❌") . " Name validation for '{$name['first']} {$name['last']}': " . 
             ($isValid ? "PASSED" : "FAILED") . "\n";
    } catch (Exception $e) {
        echo "❌ Name validation error for {$name['first']} {$name['last']}: " . $e->getMessage() . "\n";
    }
}

// Test 5: Check database tables structure
echo "\n=== DATABASE STRUCTURE CHECK ===\n";

$tables = ['students', 'registrations', 'enrollments'];
foreach ($tables as $table) {
    try {
        $columns = DB::select("DESCRIBE $table");
        echo "✅ Table '$table' exists with " . count($columns) . " columns\n";
        
        // Check for file upload columns
        $fileColumns = array_filter($columns, function($col) {
            return strpos($col->Field, '_certificate') !== false || 
                   strpos($col->Field, '_clearance') !== false ||
                   strpos($col->Field, 'diploma') !== false ||
                   strpos($col->Field, 'transcript') !== false ||
                   strpos($col->Field, 'good_moral') !== false ||
                   strpos($col->Field, 'PSA') !== false ||
                   strpos($col->Field, 'photo') !== false;
        });
        
        if (!empty($fileColumns)) {
            echo "   📁 File columns found: " . count($fileColumns) . "\n";
            foreach ($fileColumns as $col) {
                echo "      - {$col->Field} ({$col->Type})\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error checking table '$table': " . $e->getMessage() . "\n";
    }
}

// Test 6: Test file upload simulation
echo "\n=== FILE UPLOAD SIMULATION ===\n";

try {
    // Copy sample file to a temporary location to simulate upload
    $tempPath = storage_path('app/temp_test_file.png');
    if (!copy($samplePath, $tempPath)) {
        throw new Exception("Failed to copy sample file");
    }
    
    // Store file properly
    $fileName = 'test_' . time() . '_sample5.png';
    $storedPath = 'documents/' . $fileName;
    
    // Copy to storage/app/public/documents
    $publicPath = storage_path('app/public/' . $storedPath);
    
    // Ensure directory exists
    $dir = dirname($publicPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    if (copy($tempPath, $publicPath)) {
        echo "✅ File stored successfully at: $storedPath\n";
        
        // Clean up temp file
        unlink($tempPath);
        
        // Test 7: Database insertion test
        echo "\n=== DATABASE INSERTION TEST ===\n";
        
        DB::beginTransaction();
        
        try {
            // Insert test student record
            $studentId = '2025-07-TEST-' . time();
            $studentData = [
                'student_id' => $studentId,
                'user_id' => 999999, // Test user ID
                'firstname' => 'Test',
                'lastname' => 'Student',
                'email' => 'test@example.com',
                'education_level' => 'Graduate',
                'good_moral' => $storedPath, // Store file path
                'PSA' => $storedPath,
                'Course_Cert' => $storedPath,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            DB::table('students')->insert($studentData);
            echo "✅ Test student record inserted with file paths\n";
            
            // Verify the data
            $insertedStudent = DB::table('students')->where('student_id', $studentId)->first();
            if ($insertedStudent && !empty($insertedStudent->good_moral)) {
                echo "✅ File path verified in database: {$insertedStudent->good_moral}\n";
            } else {
                echo "❌ File path not found in database\n";
            }
            
            // Test registration record
            $registrationData = [
                'user_id' => 999999,
                'firstname' => 'Test',
                'lastname' => 'Student',
                'program_id' => 1,
                'package_id' => 1,
                'education_level' => 'Graduate',
                'learning_mode' => 'asynchronous',
                'enrollment_type' => 'Full',
                'status' => 'pending',
                'good_moral' => $storedPath,
                'PSA' => $storedPath,
                'Course_Cert' => $storedPath,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            $registrationId = DB::table('registrations')->insertGetId($registrationData);
            echo "✅ Test registration record inserted with ID: $registrationId\n";
            
            // Test enrollment record
            $enrollmentData = [
                'registration_id' => $registrationId,
                'user_id' => 999999,
                'program_id' => 1,
                'package_id' => 1,
                'enrollment_type' => 'Full',
                'learning_mode' => 'asynchronous',
                'enrollment_status' => 'pending',
                'payment_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            $enrollmentId = DB::table('enrollments')->insertGetId($enrollmentData);
            echo "✅ Test enrollment record inserted with ID: $enrollmentId\n";
            
            DB::rollback(); // Don't keep test data
            echo "✅ Test data cleaned up (transaction rolled back)\n";
            
        } catch (Exception $e) {
            DB::rollback();
            echo "❌ Database insertion test failed: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ Failed to store file\n";
    }
} catch (Exception $e) {
    echo "❌ File upload simulation failed: " . $e->getMessage() . "\n";
}

echo "\n=== CURRENT STUDENT DATA SAMPLE ===\n";
$recentStudents = DB::table('students')
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get(['student_id', 'firstname', 'lastname', 'education_level', 'good_moral', 'PSA', 'Course_Cert']);

foreach ($recentStudents as $student) {
    echo "👤 {$student->student_id}: {$student->firstname} {$student->lastname}\n";
    echo "   Education: {$student->education_level}\n";
    echo "   Good Moral: " . ($student->good_moral ?: 'NOT SET') . "\n";
    echo "   PSA: " . ($student->PSA ?: 'NOT SET') . "\n";
    echo "   Course Cert: " . ($student->Course_Cert ?: 'NOT SET') . "\n";
    echo "\n";
}

echo "=== TEST COMPLETED ===\n";
