<?php
// Debug script for student settings update
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\User;
use App\Models\FormRequirement;
use Illuminate\Support\Facades\Schema;

echo "=== DEBUGGING STUDENT SETTINGS UPDATE ===\n\n";

// Check student with ID 2025-07-00001
$student = Student::where('student_id', '2025-07-00001')->first();

if ($student) {
    echo "Found student: " . $student->firstname . " " . $student->lastname . "\n";
    echo "User ID: " . $student->user_id . "\n\n";
    
    // Check form requirements
    echo "=== FORM REQUIREMENTS ===\n";
    $formRequirements = FormRequirement::active()->get();
    
    echo "Active form requirements count: " . $formRequirements->count() . "\n";
    
    foreach ($formRequirements as $req) {
        $studentsColumnExists = Schema::hasColumn('students', $req->field_name);
        $registrationsColumnExists = Schema::hasColumn('registrations', $req->field_name);
        
        echo "Field: " . $req->field_name . "\n";
        echo "  - Type: " . $req->field_type . "\n";
        echo "  - Label: " . $req->field_label . "\n";
        echo "  - Required: " . ($req->is_required ? 'Yes' : 'No') . "\n";
        echo "  - Students column exists: " . ($studentsColumnExists ? 'YES' : 'NO') . "\n";
        echo "  - Registrations column exists: " . ($registrationsColumnExists ? 'YES' : 'NO') . "\n";
        echo "  - Current value in student: " . ($student->{$req->field_name} ?? 'NULL') . "\n";
        echo "\n";
    }
    
    // Check what columns exist in students table
    echo "=== STUDENTS TABLE COLUMNS ===\n";
    $studentColumns = Schema::getColumnListing('students');
    foreach ($studentColumns as $column) {
        echo "- $column\n";
    }
    
    echo "\n=== REGISTRATIONS TABLE COLUMNS ===\n";
    $registrationColumns = Schema::getColumnListing('registrations');
    foreach ($registrationColumns as $column) {
        echo "- $column\n";
    }
    
    // Test mass assignment with Student model
    echo "\n=== TESTING STUDENT MODEL MASS ASSIGNMENT ===\n";
    
    $testData = [
        'firstname' => 'Test Update',
        'lastname' => 'Test Update',
        'middlename' => 'Test Middle',
        'street_address' => 'Test Address',
    ];
    
    // Add dynamic fields to test data
    foreach ($formRequirements as $req) {
        if (Schema::hasColumn('students', $req->field_name) && $req->field_type !== 'file') {
            $testData[$req->field_name] = 'Test Value for ' . $req->field_name;
        }
    }
    
    echo "Test data to update:\n";
    foreach ($testData as $key => $value) {
        echo "  $key: $value\n";
    }
    
    try {
        // Try updating without saving
        $student->fill($testData);
        echo "\nStudent->fill() successful. Changed attributes:\n";
        foreach ($student->getDirty() as $key => $value) {
            echo "  $key: " . ($student->getOriginal($key) ?? 'NULL') . " -> $value\n";
        }
        
        // Check fillable and guarded
        echo "\nStudent model fillable:\n";
        foreach ($student->getFillable() as $field) {
            echo "  - $field\n";
        }
        
        echo "\nStudent model guarded:\n";
        foreach ($student->getGuarded() as $field) {
            echo "  - $field\n";
        }
        
    } catch (\Exception $e) {
        echo "\nError during fill(): " . $e->getMessage() . "\n";
    }
    
} else {
    echo "Student not found with ID: 2025-07-00001\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>
