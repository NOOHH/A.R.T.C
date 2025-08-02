<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Professor Module Management Routes and Functions\n";
echo "========================================================\n";

echo "Testing route definitions...\n";

// Test route existence
$routes = [
    'professor.content.update',
    'professor.content.edit', 
    'professor.content.view',
    'professor.content.archive',
    'professor.courses.archive'
];

foreach($routes as $route) {
    try {
        $url = route($route, 1); // Test with ID 1
        echo "✓ Route '$route' exists: $url\n";
    } catch (\Exception $e) {
        echo "✗ Route '$route' NOT found: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Test ContentItem methods
try {
    $content = \App\Models\ContentItem::first();
    if ($content) {
        echo "Sample content found:\n";
        echo "- ID: " . $content->id . "\n";
        echo "- Title: " . $content->content_title . "\n";
        echo "- Type: " . $content->content_type . "\n";
        echo "- Course ID: " . $content->course_id . "\n";
        
        // Test updating content
        $originalTitle = $content->content_title;
        $content->update(['content_title' => 'Test Updated Title']);
        echo "✓ Content title updated successfully\n";
        
        // Restore original title
        $content->update(['content_title' => $originalTitle]);
        echo "✓ Content title restored\n";
        
        // Test archiving
        $content->update(['is_archived' => true, 'archived_at' => now()]);
        echo "✓ Content archived successfully\n";
        
        // Restore archive status
        $content->update(['is_archived' => false, 'archived_at' => null]);
        echo "✓ Content unarchived successfully\n";
    }
} catch (\Exception $e) {
    echo "✗ Error testing content: " . $e->getMessage() . "\n";
}

echo "\n";

// Test Course methods
try {
    $course = \App\Models\Course::first();
    if ($course) {
        echo "Sample course found:\n";
        echo "- ID: " . $course->subject_id . "\n";
        echo "- Name: " . $course->subject_name . "\n";
        
        // Test archiving course
        $course->update(['is_archived' => true]);
        echo "✓ Course archived successfully\n";
        
        // Restore archive status
        $course->update(['is_archived' => false]);
        echo "✓ Course unarchived successfully\n";
    }
} catch (\Exception $e) {
    echo "✗ Error testing course: " . $e->getMessage() . "\n";
}

echo "\n";

// Test admin settings
try {
    $setting = \App\Models\AdminSetting::where('setting_key', 'professor_module_management_enabled')->first();
    echo "Professor module management enabled: " . ($setting ? $setting->setting_value : 'not set') . "\n";
} catch (\Exception $e) {
    echo "✗ Error checking admin settings: " . $e->getMessage() . "\n";
}

echo "\nAll tests completed!\n";
