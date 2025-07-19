<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Test with CORRECT database IDs
$correctFormData = [
    'user_firstname' => 'Test',
    'user_lastname' => 'User', 
    'email' => 'test123@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'selected_modules' => json_encode([['id' => '1', 'name' => 'Module 1']]),
    'program_id' => '32',         // Using actual program ID from database
    'package_id' => '18',         // Using actual package ID from database
    'learning_mode' => 'asynchronous',
    'enrollment_type' => 'Modular',
    'education_level' => 'Undergraduate',  // Using actual education level
    'Start_Date' => '2025-07-25',          // Using valid date
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

echo "=== Testing with CORRECT database IDs ===\n";
echo "Program ID: " . $correctFormData['program_id'] . " (should exist)\n";
echo "Package ID: " . $correctFormData['package_id'] . " (should exist)\n";
echo "Education Level: " . $correctFormData['education_level'] . "\n";
echo "Start Date: " . $correctFormData['Start_Date'] . "\n\n";

$validator = Validator::make($correctFormData, $rules);
if ($validator->fails()) {
    echo "❌ Validation FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "✅ Validation PASSED! The issue is with incorrect IDs being submitted.\n";
}

echo "\n=== SOLUTION ===\n";
echo "The problem is that the JavaScript form is submitting incorrect program_id and package_id values.\n";
echo "The form needs to use the actual database IDs:\n";
echo "- Programs: 32 (Engineer), 33 (Culinary), 34 (Nursing), 35 (Mechanical Engineer)\n";
echo "- Packages: 18 (Package 1), 19 (Package 2), 20 (Package 3), 21 (Package 4)\n";
echo "- Education Levels: 1 (Undergraduate), 2 (Graduate)\n";
