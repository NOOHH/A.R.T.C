#!/usr/bin/env php
<?php
/*
 * Test File Upload with OCR and Database Storage
 * Tests the complete workflow with sample5.png
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\RegistrationController;
use App\Services\OcrService;

echo "=== TESTING FILE UPLOAD WITH OCR AND DATABASE STORAGE ===\n";

// Test 1: Create a mock file upload
$samplePath = base_path('resources/images/OCR Images/sample5.png');
if (!file_exists($samplePath)) {
    echo "❌ Sample file not found\n";
    exit(1);
}

// Copy to temp location for upload simulation
$tempPath = storage_path('app/temp_upload_test.png');
copy($samplePath, $tempPath);

echo "✅ Sample file prepared for upload test\n";

// Test 2: Create mock uploaded file
$uploadedFile = new UploadedFile(
    $tempPath,
    'sample5.png',
    'image/png',
    null,
    true
);

echo "✅ Mock uploaded file created\n";

// Test 3: Create mock request with file upload
$request = new Request();
$request->files->set('file', $uploadedFile);
$request->merge([
    'field_name' => 'good_moral',
    'first_name' => 'Juanita',
    'last_name' => 'Reño'
]);

echo "✅ Mock request created with file upload\n";

// Test 4: Test the file upload validation
try {
    $controller = new RegistrationController(new OcrService());
    
    echo "🔄 Testing file upload validation...\n";
    $response = $controller->validateFileUpload($request);
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        
        if ($data['success'] ?? false) {
            echo "✅ File upload validation successful\n";
            echo "📁 File path: " . ($data['file_path'] ?? 'Not set') . "\n";
            echo "🎓 Suggestions count: " . count($data['suggestions'] ?? []) . "\n";
            
            if (!empty($data['suggestions'])) {
                foreach ($data['suggestions'] as $suggestion) {
                    echo "   - " . ($suggestion['program_name'] ?? 'Unknown') . " (Score: " . ($suggestion['score'] ?? 0) . ")\n";
                }
            }
            
            // Test 5: Now test if this file path would be stored in database during registration
            $filePath = $data['file_path'] ?? null;
            if ($filePath) {
                echo "\n🔄 Testing database storage simulation...\n";
                
                DB::beginTransaction();
                try {
                    // Simulate creating student with file path
                    $testStudentId = '2025-07-TEST-' . time();
                    
                    $studentData = [
                        'student_id' => $testStudentId,
                        'user_id' => 999999,
                        'firstname' => 'Test',
                        'lastname' => 'Student',
                        'email' => 'test@example.com',
                        'education_level' => 'Graduate',
                        'good_moral' => $filePath, // This is the key test
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    
                    DB::table('students')->insert($studentData);
                    
                    // Verify it was stored
                    $stored = DB::table('students')->where('student_id', $testStudentId)->first();
                    
                    if ($stored && !empty($stored->good_moral)) {
                        echo "✅ File path successfully stored in students table!\n";
                        echo "📁 Stored path: {$stored->good_moral}\n";
                        
                        // Check if the file actually exists
                        $fullPath = storage_path('app/public/' . $stored->good_moral);
                        if (file_exists($fullPath)) {
                            echo "✅ Physical file exists at stored path\n";
                        } else {
                            echo "⚠️  File path stored but physical file not found at: $fullPath\n";
                        }
                    } else {
                        echo "❌ File path not properly stored in database\n";
                    }
                    
                    DB::rollback(); // Clean up test data
                    echo "✅ Test data cleaned up\n";
                    
                } catch (\Exception $e) {
                    DB::rollback();
                    echo "❌ Database storage test failed: " . $e->getMessage() . "\n";
                }
            } else {
                echo "❌ No file path returned from upload validation\n";
            }
            
        } else {
            echo "❌ File upload validation failed: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Unexpected response type from upload validation\n";
    }
    
} catch (\Exception $e) {
    echo "❌ File upload test failed: " . $e->getMessage() . "\n";
    echo "📍 Error location: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test 6: Test batch creation service
echo "\n=== TESTING AUTOMATIC BATCH CREATION ===\n";

try {
    $batchService = new \App\Services\BatchCreationService();
    
    // Get a test program ID
    $program = DB::table('programs')->first();
    if (!$program) {
        echo "❌ No programs found in database\n";
    } else {
        echo "✅ Found test program: {$program->program_name} (ID: {$program->program_id})\n";
        
        DB::beginTransaction();
        try {
            // Test batch creation
            $newBatch = $batchService->createPendingBatch($program->program_id);
            
            echo "✅ Successfully created automatic batch!\n";
            echo "📋 Batch details:\n";
            echo "   ID: {$newBatch->batch_id}\n";
            echo "   Name: {$newBatch->batch_name}\n";
            echo "   Program: {$newBatch->program_id}\n";
            echo "   Capacity: {$newBatch->current_capacity}/{$newBatch->max_capacity}\n";
            echo "   Status: {$newBatch->batch_status}\n";
            echo "   Registration Deadline: {$newBatch->registration_deadline}\n";
            echo "   Start Date: {$newBatch->start_date}\n";
            echo "   End Date: {$newBatch->end_date}\n";
            
            DB::rollback(); // Clean up test data
            echo "✅ Test batch cleaned up\n";
            
        } catch (\Exception $e) {
            DB::rollback();
            echo "❌ Batch creation test failed: " . $e->getMessage() . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Batch service test failed: " . $e->getMessage() . "\n";
}

// Cleanup
if (file_exists($tempPath)) {
    unlink($tempPath);
}

echo "\n=== ALL TESTS COMPLETED ===\n";
