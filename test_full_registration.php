#!/usr/bin/env php
<?php
/*
 * Test Complete Registration Flow with File Upload
 * Simulates the exact process a user would go through
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\RegistrationController;
use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "=== COMPLETE REGISTRATION FLOW TEST ===\n";

// Step 1: Prepare the sample file
$samplePath = base_path('resources/images/OCR Images/sample5.png');
if (!file_exists($samplePath)) {
    echo "❌ Sample file not found\n";
    exit(1);
}

echo "✅ Found sample file: " . basename($samplePath) . "\n";

// Step 2: Copy file to temp location for upload simulation
$tempPath = storage_path('app/temp_sample5.png');
copy($samplePath, $tempPath);

// Step 3: Create uploaded file object
$uploadedFile = new UploadedFile(
    $tempPath,
    'sample5.png', 
    'image/png',
    null,
    true // Mark as test
);

echo "✅ Created uploaded file object\n";

// Step 4: Test OCR validation first
echo "\n🔄 Testing file upload validation...\n";

try {
    $request = new Request();
    $request->files->set('file', $uploadedFile);
    $request->merge([
        'field_name' => 'good_moral',
        'first_name' => 'Juanita',
        'last_name' => 'Reño'
    ]);
    
    $controller = new RegistrationController(new OcrService());
    $response = $controller->validateFileUpload($request);
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        
        if ($data['success'] ?? false) {
            echo "✅ OCR validation successful!\n";
            echo "📁 File stored at: " . ($data['file_path'] ?? 'Unknown') . "\n";
            echo "🎓 Program suggestions: " . count($data['suggestions'] ?? []) . "\n";
            
            foreach (($data['suggestions'] ?? []) as $suggestion) {
                echo "   - " . ($suggestion['program_name'] ?? 'Unknown') . "\n";
            }
            
            $validatedFilePath = $data['file_path'] ?? null;
            
        } else {
            echo "❌ OCR validation failed: " . ($data['message'] ?? 'Unknown error') . "\n";
            $validatedFilePath = null;
        }
    } else {
        echo "❌ Unexpected response from OCR validation\n";
        $validatedFilePath = null;
    }
    
} catch (\Exception $e) {
    echo "❌ OCR validation error: " . $e->getMessage() . "\n";
    $validatedFilePath = null;
}

// Step 5: Test complete registration process if OCR validation worked
if ($validatedFilePath) {
    echo "\n🔄 Testing complete registration process...\n";
    
    DB::beginTransaction();
    
    try {
        // Create a new uploaded file for the registration
        $tempPath2 = storage_path('app/temp_sample5_reg.png');
        copy($samplePath, $tempPath2);
        
        $uploadedFile2 = new UploadedFile(
            $tempPath2,
            'sample5.png',
            'image/png',
            null,
            true
        );
        
        // Create registration request
        $regRequest = new Request();
        $regRequest->files->set('good_moral', $uploadedFile2);
        $regRequest->merge([
            'user_firstname' => 'Test',
            'user_lastname' => 'Student',
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'firstname' => 'Test',
            'lastname' => 'Student',
            'program_id' => 1,
            'package_id' => 1,
            'enrollment_type' => 'Full',
            'learning_mode' => 'synchronous',
            'education_level' => 'Graduate',
            'Start_Date' => now()->addDays(7)->format('Y-m-d'),
            'good_moral_path' => $validatedFilePath // Include validated file path
        ]);
        
        // Add headers to simulate AJAX
        $regRequest->headers->set('Accept', 'application/json');
        $regRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        $studentController = new StudentRegistrationController();
        $regResponse = $studentController->store($regRequest);
        
        if ($regResponse instanceof \Illuminate\Http\JsonResponse) {
            $regData = $regResponse->getData(true);
            
            if ($regData['success'] ?? false) {
                echo "✅ Registration successful!\n";
                echo "📋 Registration ID: " . ($regData['data']['registration_id'] ?? 'Unknown') . "\n";
                echo "👤 User ID: " . ($regData['data']['user_id'] ?? 'Unknown') . "\n";
                echo "🎓 Batch ID: " . ($regData['data']['batch_id'] ?? 'None - Asynchronous') . "\n";
                
                // Check if student record was created with file path
                $userId = $regData['data']['user_id'] ?? null;
                if ($userId) {
                    $student = DB::table('students')->where('user_id', $userId)->first();
                    if ($student) {
                        echo "✅ Student record created: {$student->student_id}\n";
                        echo "📁 File paths in student record:\n";
                        echo "   Good Moral: " . ($student->good_moral ?: 'NULL') . "\n";
                        echo "   PSA: " . ($student->PSA ?: 'NULL') . "\n";
                        echo "   Course Cert: " . ($student->Course_Cert ?: 'NULL') . "\n";
                        
                        if (!empty($student->good_moral)) {
                            echo "✅ SUCCESS: File path stored in student database!\n";
                        } else {
                            echo "❌ ISSUE: File path not stored in student database\n";
                        }
                    } else {
                        echo "❌ Student record not found\n";
                    }
                    
                    // Check enrollment record
                    $enrollment = DB::table('enrollments')->where('user_id', $userId)->first();
                    if ($enrollment) {
                        echo "✅ Enrollment record created: {$enrollment->enrollment_id}\n";
                        if ($enrollment->batch_id) {
                            echo "🎓 Assigned to batch: {$enrollment->batch_id}\n";
                            
                            // Check if batch was auto-created
                            $batch = DB::table('student_batches')->where('batch_id', $enrollment->batch_id)->first();
                            if ($batch) {
                                echo "📋 Batch details:\n";
                                echo "   Name: {$batch->batch_name}\n";
                                echo "   Capacity: {$batch->current_capacity}/{$batch->max_capacity}\n";
                                echo "   Status: {$batch->batch_status}\n";
                                echo "   Start Date: {$batch->start_date}\n";
                                echo "   End Date: {$batch->end_date}\n";
                                
                                if (strpos($batch->description, 'Auto-created') !== false) {
                                    echo "✅ SUCCESS: Batch was automatically created!\n";
                                } else {
                                    echo "ℹ️  Batch was pre-existing\n";
                                }
                            }
                        } else {
                            echo "ℹ️  No batch assigned (asynchronous mode)\n";
                        }
                    } else {
                        echo "❌ Enrollment record not found\n";
                    }
                }
                
            } else {
                echo "❌ Registration failed: " . ($regData['message'] ?? 'Unknown error') . "\n";
                if (isset($regData['errors'])) {
                    foreach ($regData['errors'] as $field => $errors) {
                        echo "   $field: " . implode(', ', $errors) . "\n";
                    }
                }
            }
        } else {
            echo "❌ Unexpected response from registration\n";
        }
        
        DB::rollback(); // Clean up test data
        echo "✅ Test data cleaned up\n";
        
        // Clean up temp files
        if (file_exists($tempPath2)) {
            unlink($tempPath2);
        }
        
    } catch (\Exception $e) {
        DB::rollback();
        echo "❌ Registration test failed: " . $e->getMessage() . "\n";
        echo "📍 Error location: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
    
} else {
    echo "⚠️  Skipping registration test due to OCR validation failure\n";
}

// Cleanup
if (file_exists($tempPath)) {
    unlink($tempPath);
}

echo "\n=== TEST COMPLETED ===\n";
echo "\nTo test manually:\n";
echo "1. Open: http://localhost:8000/Full_enrollment\n";
echo "2. Upload: resources/images/OCR Images/sample5.png\n";
echo "3. Use name: Juanita Reño\n";
echo "4. Check database tables after registration\n";
