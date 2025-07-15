<?php
// Test script to verify student settings update
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\FormRequirement;
use Illuminate\Support\Facades\Log;

echo "=== TESTING STUDENT UPDATE ===\n\n";

// Find student 2025-07-00001
$student = Student::where('student_id', '2025-07-00001')->first();

if (!$student) {
    echo "Student not found!\n";
    exit;
}

echo "Found student: " . $student->firstname . " " . $student->lastname . "\n";
echo "User ID: " . $student->user_id . "\n\n";

// Get active form requirements
$formRequirements = FormRequirement::active()->get();
echo "Active form requirements: " . $formRequirements->count() . "\n\n";

// Test updating the student with some dynamic field values
$testUpdateData = [
    'firstname' => 'Updated Test Name',
    'lastname' => 'Updated Last Name',
    'middlename' => 'Updated Middle',
    'street_address' => 'Updated Address 123'
];

// Add some dynamic fields
foreach ($formRequirements as $req) {
    if ($req->field_type !== 'section' && $req->field_type !== 'file') {
        $testUpdateData[$req->field_name] = 'Test Value ' . date('H:i:s');
        echo "Adding dynamic field: " . $req->field_name . " = " . $testUpdateData[$req->field_name] . "\n";
    }
}

echo "\nAttempting to update student with test data...\n";

try {
    $student->update($testUpdateData);
    echo "SUCCESS: Student updated successfully!\n\n";
    
    // Refresh and show updated values
    $student->refresh();
    echo "Updated student data:\n";
    foreach ($testUpdateData as $key => $value) {
        $currentValue = $student->{$key} ?? 'NULL';
        echo "  {$key}: {$currentValue}\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: Failed to update student\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
