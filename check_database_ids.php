<?php
// Check if the test IDs in the HTML form exist in database
require_once 'vendor/autoload.php';

// Laravel app bootstrap
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING DATABASE IDS ===\n";

// Check Program ID 33
$program = \App\Models\Program::find(33);
echo "\nProgram ID 33: " . ($program ? "EXISTS - " . $program->program_name : "NOT FOUND") . "\n";

// Check Module ID 54
$module = \App\Models\Module::find(54);
echo "Module ID 54: " . ($module ? "EXISTS - " . $module->module_name . " (Program: " . $module->program_id . ")" : "NOT FOUND") . "\n";

// Check Course ID 24
$course = \App\Models\Course::find(24);
echo "Course ID 24: " . ($course ? "EXISTS - " . $course->subject_name . " (Module: " . $course->module_id . ")" : "NOT FOUND") . "\n";

// Get some actual valid IDs for testing
echo "\n=== GETTING VALID IDS FOR TESTING ===\n";

$firstProgram = \App\Models\Program::first();
if ($firstProgram) {
    echo "First Program: ID {$firstProgram->program_id} - {$firstProgram->program_name}\n";
    
    $firstModule = \App\Models\Module::where('program_id', $firstProgram->program_id)->first();
    if ($firstModule) {
        echo "First Module in Program: ID {$firstModule->modules_id} - {$firstModule->module_name}\n";
        
        $firstCourse = \App\Models\Course::where('module_id', $firstModule->modules_id)->first();
        if ($firstCourse) {
            echo "First Course in Module: ID {$firstCourse->subject_id} - {$firstCourse->subject_name}\n";
        } else {
            echo "No courses found in module {$firstModule->modules_id}\n";
            // Create a test course
            $testCourse = new \App\Models\Course();
            $testCourse->subject_name = "Test Course for Content";
            $testCourse->subject_description = "Test course for content testing";
            $testCourse->module_id = $firstModule->modules_id;
            $testCourse->save();
            echo "Created test course: ID {$testCourse->subject_id}\n";
        }
    } else {
        echo "No modules found in program {$firstProgram->program_id}\n";
    }
} else {
    echo "No programs found in database\n";
}

// Check ContentItems structure
echo "\n=== CONTENT ITEMS TABLE STRUCTURE ===\n";
$contentItemSample = \App\Models\ContentItem::first();
if ($contentItemSample) {
    echo "Sample ContentItem fields:\n";
    foreach ($contentItemSample->getAttributes() as $key => $value) {
        echo "  $key: " . ($value ?: 'NULL') . "\n";
    }
} else {
    echo "No ContentItems found - table might be empty\n";
}

// Check recent content items
echo "\n=== RECENT CONTENT ITEMS ===\n";
$recentItems = \App\Models\ContentItem::orderBy('created_at', 'desc')->limit(5)->get();
foreach ($recentItems as $item) {
    echo "ID: {$item->id} | Title: {$item->content_title} | Course: {$item->course_id} | Attachment: " . ($item->attachment_path ?: 'NULL') . "\n";
}

echo "\n=== CHECK COMPLETE ===\n";
?>
