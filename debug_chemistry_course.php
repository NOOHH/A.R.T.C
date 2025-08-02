<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Investigating Chemistry Course Issues\n";
echo "===================================\n";

// Find Chemistry course
$chemistryCourse = \App\Models\Course::where('subject_name', 'LIKE', '%Chemistry%')->first();
if (!$chemistryCourse) {
    $chemistryCourse = \App\Models\Course::where('subject_name', 'LIKE', '%chemistry%')->first();
}

if ($chemistryCourse) {
    echo "Chemistry course found:\n";
    echo "- ID: " . $chemistryCourse->subject_id . "\n";
    echo "- Name: " . $chemistryCourse->subject_name . "\n";
    echo "- Description: " . ($chemistryCourse->subject_description ?? 'No description') . "\n";
    echo "- Module ID: " . $chemistryCourse->module_id . "\n";
    echo "- Is Archived: " . ($chemistryCourse->is_archived ? 'YES' : 'NO') . "\n";
    echo "- Created: " . $chemistryCourse->created_at . "\n";
    
    // Check content items for this course
    $contentItems = \App\Models\ContentItem::where('course_id', $chemistryCourse->subject_id)->get();
    echo "\nContent items for Chemistry course: " . $contentItems->count() . "\n";
    
    foreach($contentItems as $content) {
        echo "\nContent ID: " . $content->id . "\n";
        echo "- Title: " . $content->content_title . "\n";
        echo "- Type: " . $content->content_type . "\n";
        echo "- Description: " . ($content->content_description ?? 'No description') . "\n";
        echo "- Is Archived: " . ($content->is_archived ? 'YES' : 'NO') . "\n";
        echo "- Created: " . $content->created_at . "\n";
    }
    
    // Check the module this course belongs to
    $module = \App\Models\Module::find($chemistryCourse->module_id);
    if ($module) {
        echo "\nModule info:\n";
        echo "- Module ID: " . $module->modules_id . "\n";
        echo "- Module Name: " . $module->module_name . "\n";
        echo "- Program ID: " . $module->program_id . "\n";
        
        // Check if professor has access to this program
        $professor = \App\Models\Professor::find(8); // Assuming professor ID 8
        if ($professor) {
            $hasAccess = $professor->programs()->where('program_id', $module->program_id)->exists();
            echo "- Professor has access to program: " . ($hasAccess ? 'YES' : 'NO') . "\n";
        }
    }
} else {
    echo "Chemistry course not found. Let me list all courses:\n";
    $courses = \App\Models\Course::take(10)->get();
    foreach($courses as $course) {
        echo "- ID: " . $course->subject_id . " | Name: " . $course->subject_name . "\n";
    }
}

// Check content item that might be "Lessons 1"
echo "\n\nLooking for 'Lessons 1' content:\n";
$lessonsContent = \App\Models\ContentItem::where('content_title', 'LIKE', '%Lessons 1%')->get();
foreach($lessonsContent as $content) {
    echo "\nLessons 1 Content found:\n";
    echo "- ID: " . $content->id . "\n";
    echo "- Title: " . $content->content_title . "\n";
    echo "- Course ID: " . $content->course_id . "\n";
    echo "- Type: " . $content->content_type . "\n";
    echo "- Is Archived: " . ($content->is_archived ? 'YES' : 'NO') . "\n";
    
    // Check the course this content belongs to
    $course = \App\Models\Course::find($content->course_id);
    if ($course) {
        echo "- Course Name: " . $course->subject_name . "\n";
    }
}
