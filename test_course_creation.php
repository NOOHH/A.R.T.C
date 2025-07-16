<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the course creation API
echo "Testing Course Creation API...\n";

// Get first program and its modules
$program = App\Models\Program::first();
$module = App\Models\Module::where('program_id', $program->program_id)->first();

if (!$program || !$module) {
    echo "Error: No program or module found\n";
    exit(1);
}

echo "Program: {$program->program_name}\n";
echo "Module: {$module->module_name}\n";

// Test data
$testData = [
    'subject_name' => 'Test Course',
    'subject_description' => 'Test Description',
    'module_id' => $module->modules_id,
    'subject_price' => 100.00,
    'is_required' => false,
];

try {
    // Simulate course creation
    $course = App\Models\Course::create([
        'subject_name' => $testData['subject_name'],
        'subject_description' => $testData['subject_description'],
        'module_id' => $testData['module_id'],
        'subject_price' => $testData['subject_price'],
        'is_required' => $testData['is_required'],
        'is_active' => true,
        'subject_order' => App\Models\Course::where('module_id', $testData['module_id'])->max('subject_order') + 1,
    ]);

    echo "✅ Course created successfully!\n";
    echo "Course ID: {$course->subject_id}\n";
    echo "Course Name: {$course->subject_name}\n";
    
    // Clean up - delete the test course
    $course->delete();
    echo "✅ Test course cleaned up\n";
    
} catch (Exception $e) {
    echo "❌ Error creating course: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "Test completed.\n";
