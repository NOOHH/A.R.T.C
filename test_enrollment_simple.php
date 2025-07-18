<?php
/**
 * Simple Modular Enrollment Test Script
 * Run this file directly: php test_enrollment_simple.php
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StudentRegistrationController;
use Illuminate\Http\Request;

echo "=== Modular Enrollment Test Script ===\n";

// Test data
$testData = [
    'user_firstname' => 'Test',
    'user_lastname' => 'Student',
    'email' => 'test.student.' . time() . '@example.com', // Unique email
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'program_id' => 1,
    'package_id' => 1,
    'learning_mode' => 'synchronous',
    'enrollment_type' => 'Modular',
    'education_level' => 'Undergraduate',
    'selected_modules' => '[{"id": 1, "name": "Module 1"}]',
    'Start_Date' => '2025-02-01',
    'plan_id' => 2,
    'referral_code' => ''
];

echo "Test Data Prepared:\n";
foreach ($testData as $key => $value) {
    if ($key !== 'password' && $key !== 'password_confirmation') {
        echo "  {$key}: {$value}\n";
    } else {
        echo "  {$key}: [HIDDEN]\n";
    }
}
echo "\n";

// Test 1: Check if required tables exist
echo "1. Checking required tables...\n";
$tables = ['users', 'students', 'registrations', 'enrollments', 'packages', 'programs'];
foreach ($tables as $table) {
    try {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        echo "   {$table}: " . ($exists ? "✅ Exists" : "❌ Missing") . "\n";
    } catch (Exception $e) {
        echo "   {$table}: ❌ Error - " . $e->getMessage() . "\n";
    }
}
echo "\n";

// Test 2: Check if test data references exist
echo "2. Checking test data validity...\n";
try {
    $program = DB::table('programs')->where('program_id', $testData['program_id'])->first();
    echo "   Program ID {$testData['program_id']}: " . ($program ? "✅ Exists - {$program->program_name}" : "❌ Not found") . "\n";
    
    $package = DB::table('packages')->where('package_id', $testData['package_id'])->first();
    echo "   Package ID {$testData['package_id']}: " . ($package ? "✅ Exists - {$package->package_name}" : "❌ Not found") . "\n";
} catch (Exception $e) {
    echo "   Error checking data: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Test the enrollment submission
echo "3. Testing enrollment submission...\n";
try {
    $controller = new StudentRegistrationController();
    
    // Create a mock request
    $request = new Request($testData);
    
    // Set the request method and headers
    $request->setMethod('POST');
    $request->headers->set('Content-Type', 'application/json');
    $request->headers->set('Accept', 'application/json');
    
    echo "   Calling submitModularEnrollment method...\n";
    $response = $controller->submitModularEnrollment($request);
    
    // Get response content
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        echo "   ✅ Enrollment successful!\n";
        echo "   User ID: " . $responseData['data']['user_id'] . "\n";
        echo "   Student ID: " . $responseData['data']['student_id'] . "\n";
        echo "   Enrollment ID: " . $responseData['data']['enrollment_id'] . "\n";
        echo "   Registration ID: " . $responseData['data']['registration_id'] . "\n";
        
        // Verify data was saved
        echo "\n4. Verifying saved data...\n";
        $user = DB::table('users')->where('user_id', $responseData['data']['user_id'])->first();
        echo "   User saved: " . ($user ? "✅ Yes - {$user->user_firstname} {$user->user_lastname}" : "❌ No") . "\n";
        
        $student = DB::table('students')->where('student_id', $responseData['data']['student_id'])->first();
        echo "   Student saved: " . ($student ? "✅ Yes - {$student->student_firstname} {$student->student_lastname}" : "❌ No") . "\n";
        
        $registration = DB::table('registrations')->where('registration_id', $responseData['data']['registration_id'])->first();
        echo "   Registration saved: " . ($registration ? "✅ Yes - {$registration->firstname} {$registration->lastname}" : "❌ No") . "\n";
        
        $enrollment = DB::table('enrollments')->where('enrollment_id', $responseData['data']['enrollment_id'])->first();
        echo "   Enrollment saved: " . ($enrollment ? "✅ Yes - Type: {$enrollment->enrollment_type}" : "❌ No") . "\n";
        
    } else {
        echo "   ❌ Enrollment failed!\n";
        echo "   Message: " . $responseData['message'] . "\n";
        if (isset($responseData['errors'])) {
            echo "   Errors:\n";
            foreach ($responseData['errors'] as $field => $errors) {
                echo "     {$field}: " . implode(', ', $errors) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ Test failed with exception: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
