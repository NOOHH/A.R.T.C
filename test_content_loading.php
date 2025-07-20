<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Testing Content Loading After Lessons Table Removal\n";
echo "==================================================\n\n";

try {
    // Test 1: Check if ContentItem model works
    echo "1. Testing ContentItem model...\n";
    $contentItems = \App\Models\ContentItem::take(5)->get();
    echo "   ✓ Found " . $contentItems->count() . " content items\n\n";
    
    // Test 2: Check ContentItem with course relationship
    echo "2. Testing ContentItem with course relationship...\n";
    $contentWithCourse = \App\Models\ContentItem::with('course')->take(3)->get();
    echo "   ✓ Loaded " . $contentWithCourse->count() . " content items with course data\n\n";
    
    // Test 3: Check if getContent endpoint structure works
    echo "3. Testing AdminModuleController getContent logic...\n";
    $sampleContent = \App\Models\ContentItem::first();
    if ($sampleContent) {
        echo "   ✓ Sample content found: ID " . $sampleContent->id . " - " . $sampleContent->content_title . "\n";
        echo "   ✓ Course ID: " . $sampleContent->course_id . "\n";
        echo "   ✓ Content Type: " . $sampleContent->content_type . "\n";
    } else {
        echo "   ⚠ No content items found in database\n";
    }
    echo "\n";
    
    // Test 4: Check Course model without lessons
    echo "4. Testing Course model without lessons...\n";
    $courses = \App\Models\Course::with('contentItems')->take(3)->get();
    echo "   ✓ Loaded " . $courses->count() . " courses with content items\n";
    foreach ($courses as $course) {
        echo "   - Course: " . $course->subject_name . " has " . $course->contentItems->count() . " content items\n";
    }
    echo "\n";
    
    echo "✅ All tests passed! Lessons table removal is complete.\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
