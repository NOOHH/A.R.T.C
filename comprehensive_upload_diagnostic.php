<?php
// Comprehensive diagnostic for course content upload issue
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== COMPREHENSIVE COURSE CONTENT UPLOAD DIAGNOSTIC ===\n";

// 1. Check if test IDs exist
echo "\n1. CHECKING TEST IDS FROM HTML FORM:\n";
$program = \App\Models\Program::find(33);
echo "Program 33: " . ($program ? "✅ EXISTS - " . $program->program_name : "❌ NOT FOUND") . "\n";

$module = \App\Models\Module::find(54);
echo "Module 54: " . ($module ? "✅ EXISTS - " . $module->module_name . " (Program: " . $module->program_id . ")" : "❌ NOT FOUND") . "\n";

$course = \App\Models\Course::find(24);
echo "Course 24: " . ($course ? "✅ EXISTS - " . $course->subject_name . " (Module: " . $course->module_id . ")" : "❌ NOT FOUND") . "\n";

// Get valid IDs
echo "\n2. GETTING VALID IDS FOR TESTING:\n";
$validProgram = \App\Models\Program::first();
$validModule = null;
$validCourse = null;

if ($validProgram) {
    echo "✅ Valid Program: ID {$validProgram->program_id} - {$validProgram->program_name}\n";
    
    $validModule = \App\Models\Module::where('program_id', $validProgram->program_id)->first();
    if ($validModule) {
        echo "✅ Valid Module: ID {$validModule->modules_id} - {$validModule->module_name}\n";
        
        $validCourse = \App\Models\Course::where('module_id', $validModule->modules_id)->first();
        if ($validCourse) {
            echo "✅ Valid Course: ID {$validCourse->subject_id} - {$validCourse->subject_name}\n";
        } else {
            // Create a test course
            try {
                $validCourse = new \App\Models\Course();
                $validCourse->subject_name = "Test Course for Content Upload";
                $validCourse->subject_description = "Auto-created for testing";
                $validCourse->module_id = $validModule->modules_id;
                $validCourse->is_active = true;
                $validCourse->save();
                echo "✅ Created Valid Course: ID {$validCourse->subject_id} - {$validCourse->subject_name}\n";
            } catch (Exception $e) {
                echo "❌ Failed to create course: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "❌ No modules found in program\n";
    }
} else {
    echo "❌ No programs found\n";
}

// 3. Test validation rules
echo "\n3. TESTING VALIDATION RULES:\n";
if ($validProgram && $validModule && $validCourse) {
    $testData = [
        'program_id' => $validProgram->program_id,
        'module_id' => $validModule->modules_id,
        'course_id' => $validCourse->subject_id,
        'content_type' => 'lesson',
        'content_title' => 'Test Content',
        'content_description' => 'Test description',
    ];
    
    echo "Test data prepared:\n";
    foreach ($testData as $key => $value) {
        echo "  $key: $value\n";
    }
    
    // Test validation manually
    $rules = [
        'program_id' => 'required|exists:programs,program_id',
        'module_id' => 'required|exists:modules,modules_id',
        'course_id' => 'required|exists:courses,subject_id',
        'content_type' => 'required|in:lesson,quiz,test,assignment,pdf,link,video,document',
        'content_title' => 'required|string|max:255',
        'content_description' => 'nullable|string',
    ];
    
    $validator = \Illuminate\Support\Facades\Validator::make($testData, $rules);
    
    if ($validator->fails()) {
        echo "❌ VALIDATION FAILED:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - $error\n";
        }
    } else {
        echo "✅ VALIDATION PASSED\n";
    }
    
    // Test ContentItem creation
    echo "\n4. TESTING CONTENT ITEM CREATION:\n";
    try {
        $contentItemData = [
            'content_title' => 'Test Content Item',
            'content_description' => 'Test description',
            'course_id' => $validCourse->subject_id,
            'content_type' => 'lesson',
            'attachment_path' => 'content/test_file.pdf',
            'is_required' => true,
            'is_active' => true,
        ];
        
        $contentItem = \App\Models\ContentItem::create($contentItemData);
        echo "✅ ContentItem created successfully:\n";
        echo "  - ID: {$contentItem->id}\n";
        echo "  - Title: {$contentItem->content_title}\n";
        echo "  - Course ID: {$contentItem->course_id}\n";
        echo "  - Attachment Path: " . ($contentItem->attachment_path ?: 'NULL') . "\n";
        
        // Clean up
        $contentItem->delete();
        echo "  - Test record deleted\n";
        
    } catch (Exception $e) {
        echo "❌ ContentItem creation failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Cannot test validation - missing valid IDs\n";
}

// 4. Check recent uploads and their status
echo "\n5. CHECKING RECENT CONTENT ITEMS:\n";
$recentItems = \App\Models\ContentItem::orderBy('created_at', 'desc')->limit(10)->get();
echo "Found {$recentItems->count()} recent content items:\n";

foreach ($recentItems as $item) {
    echo "  ID: {$item->id} | Title: {$item->content_title} | Course: {$item->course_id} | ";
    echo "Attachment: " . ($item->attachment_path ?: 'NULL') . " | ";
    echo "Created: {$item->created_at}\n";
}

// 5. Check storage directory
echo "\n6. CHECKING STORAGE DIRECTORY:\n";
$contentDir = storage_path('app/public/content');
$publicDir = public_path('storage/content');

echo "Content directory: $contentDir\n";
echo "  Exists: " . (is_dir($contentDir) ? '✅ YES' : '❌ NO') . "\n";
echo "  Writable: " . (is_writable($contentDir) ? '✅ YES' : '❌ NO') . "\n";

echo "Public directory: $publicDir\n";
echo "  Exists: " . (is_dir($publicDir) ? '✅ YES' : '❌ NO') . "\n";

// Create directories if they don't exist
if (!is_dir($contentDir)) {
    mkdir($contentDir, 0755, true);
    echo "  ✅ Created content directory\n";
}

if (!is_dir($publicDir)) {
    mkdir($publicDir, 0755, true);
    echo "  ✅ Created public directory\n";
}

// Test file operations
echo "\n7. TESTING FILE OPERATIONS:\n";
$testFile = $contentDir . '/test_file.txt';
$testContent = "Test file content for upload testing";

if (file_put_contents($testFile, $testContent)) {
    echo "✅ Test file created successfully: $testFile\n";
    echo "  File size: " . filesize($testFile) . " bytes\n";
    
    // Copy to public
    $publicTestFile = $publicDir . '/test_file.txt';
    if (copy($testFile, $publicTestFile)) {
        echo "✅ Test file copied to public successfully\n";
    } else {
        echo "❌ Failed to copy test file to public\n";
    }
    
    // Clean up
    unlink($testFile);
    if (file_exists($publicTestFile)) {
        unlink($publicTestFile);
    }
    echo "  ✅ Test files cleaned up\n";
} else {
    echo "❌ Failed to create test file\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "UPDATE YOUR HTML TEST FILE WITH THESE VALID IDS:\n";
if ($validProgram && $validModule && $validCourse) {
    echo "program_id: {$validProgram->program_id}\n";
    echo "module_id: {$validModule->modules_id}\n";
    echo "course_id: {$validCourse->subject_id}\n";
}
?>
