<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing Chemistry Course Issues\n";
echo "==============================\n";

// 1. Unarchive the "Lessons 1" content
$lessonsContent = \App\Models\ContentItem::find(83);
if ($lessonsContent) {
    $lessonsContent->update([
        'is_archived' => false,
        'archived_at' => null
    ]);
    echo "✓ Unarchived 'Lessons 1' content (ID: 83)\n";
}

// 2. Check the getCourseContent method to see if it filters archived content
echo "\nChecking content display logic...\n";

// Let's check what the getCourseContent method actually returns
$courseId = 48; // Chemistry course ID

try {
    $content = \App\Models\ContentItem::where('course_id', $courseId)
        ->where('is_archived', false) // This might be filtering out content
        ->get();
    
    echo "Active content for Chemistry course: " . $content->count() . "\n";
    foreach($content as $item) {
        echo "- ID: " . $item->id . " | Title: " . $item->content_title . " | Type: " . $item->content_type . "\n";
    }
    
    // Check all content (including archived)
    $allContent = \App\Models\ContentItem::where('course_id', $courseId)->get();
    echo "\nAll content for Chemistry course: " . $allContent->count() . "\n";
    foreach($allContent as $item) {
        echo "- ID: " . $item->id . " | Title: " . $item->content_title . " | Archived: " . ($item->is_archived ? 'YES' : 'NO') . "\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nCompleted fixes!\n";
