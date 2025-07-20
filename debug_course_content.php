<?php
// debug_course_content.php - Simple debug endpoint

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate the exact same request as the JavaScript
$testData = [
    'program_id' => 32,
    'module_id' => 15,
    'course_id' => 10,
    'content_type' => 'lesson',
    'content_title' => 'Test Content Title',
    'content_description' => 'Test Description',
];

echo "Testing course content creation...\n";
echo "==================================\n";

try {
    // Simulate validation
    $requiredFields = ['program_id', 'module_id', 'course_id', 'content_type', 'content_title'];
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($testData[$field]) || $testData[$field] === null || $testData[$field] === '') {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        echo "Missing required fields: " . implode(', ', $missingFields) . "\n";
        exit(1);
    }
    
    echo "All required fields present\n";
    
    // Test if referenced records exist
    $program = DB::table('programs')->where('program_id', $testData['program_id'])->first();
    if (!$program) {
        echo "ERROR: Program with ID {$testData['program_id']} not found\n";
        exit(1);
    }
    echo "Program exists: {$program->program_name}\n";
    
    $module = DB::table('modules')->where('modules_id', $testData['module_id'])->first();
    if (!$module) {
        echo "ERROR: Module with ID {$testData['module_id']} not found\n";
        exit(1);
    }
    echo "Module exists: {$module->module_name}\n";
    
    $course = DB::table('courses')->where('subject_id', $testData['course_id'])->first();
    if (!$course) {
        echo "ERROR: Course with ID {$testData['course_id']} not found\n";
        exit(1);
    }
    echo "Course exists: {$course->subject_name}\n";
    
    // Test ContentItem creation
    echo "\nTesting ContentItem creation...\n";
    $contentItem = \App\Models\ContentItem::create([
        'content_title' => $testData['content_title'],
        'content_description' => $testData['content_description'],
        'course_id' => $course->subject_id,
        'content_type' => $testData['content_type'],
        'is_active' => true,
    ]);
    
    echo "SUCCESS: ContentItem created with ID: " . $contentItem->id . "\n";
    
    // Clean up test record
    $contentItem->delete();
    echo "Test record cleaned up.\n";
    
    echo "\nSuccess! Database operations are working correctly.\n";
    echo "The issue is likely in the controller validation or error handling.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Exception type: " . get_class($e) . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
