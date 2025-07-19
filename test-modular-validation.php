<?php
require_once 'vendor/autoload.php';

// Initialize Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Mock form data that matches what the user is submitting
$formData = [
    'user_firstname' => 'Test',
    'user_lastname' => 'User', 
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'selected_modules' => json_encode([['id' => '1', 'name' => 'Module 1']]),
    'program_id' => '1',
    'package_id' => '1', 
    'learning_mode' => 'asynchronous',
    'enrollment_type' => 'Modular',
    'education_level' => '',  // This might be missing
    'Start_Date' => '',       // This might be missing
];

// Validation rules from the controller
$rules = [
    'program_id' => 'required|exists:programs,program_id',
    'package_id' => 'required|exists:packages,package_id',
    'learning_mode' => 'required|in:synchronous,asynchronous',
    'batch_id' => 'nullable|exists:student_batches,batch_id',
    'selected_modules' => 'required|string',
    'education_level' => 'required|string',
    'Start_Date' => 'required|date',
    'enrollment_type' => 'required|in:Modular',
    'plan_id' => 'nullable|integer',
    'referral_code' => 'nullable|string',
    'user_firstname' => 'required|string|max:255',
    'user_lastname' => 'required|string|max:255', 
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:8|confirmed',
];

echo "Testing form validation...\n\n";

// Test with empty education_level and Start_Date
echo "=== Test 1: Missing education_level and Start_Date ===\n";
$validator = Validator::make($formData, $rules);
if ($validator->fails()) {
    echo "Validation FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "Validation PASSED\n";
}

echo "\n";

// Test with filled education_level and Start_Date
echo "=== Test 2: With education_level and Start_Date ===\n";
$formData['education_level'] = 'college';
$formData['Start_Date'] = date('Y-m-d');

$validator = Validator::make($formData, $rules);
if ($validator->fails()) {
    echo "Validation FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "Validation PASSED\n";
}

echo "\n";

// Display current database state
echo "=== Database Check ===\n";
try {
    $programs = \App\Models\Program::count();
    echo "Programs in database: $programs\n";
    
    $packages = \App\Models\Package::count(); 
    echo "Packages in database: $packages\n";
    
    $users = \App\Models\User::where('email', 'test@example.com')->count();
    echo "Users with test@example.com: $users\n";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
