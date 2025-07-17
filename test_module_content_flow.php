<?php

// Quick test to check the data flow and relationships

use App\Models\Module;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\ContentItem;

// Get a specific module (from the URL, it seems you're viewing module with ID from the session)
$moduleId = 45; // Change this to the module ID you're trying to view

echo "<h2>Testing Data Flow for Module ID: {$moduleId}</h2>";

// 1. Check if module exists
$module = Module::find($moduleId);
if (!$module) {
    echo "<p style='color: red;'>❌ Module {$moduleId} not found!</p>";
    exit;
}
echo "<p style='color: green;'>✅ Module found: {$module->module_name}</p>";

// 2. Check courses linked to this module
$courses = Course::where('module_id', $moduleId)->get();
echo "<h3>Courses for this module:</h3>";
if ($courses->isEmpty()) {
    echo "<p style='color: red;'>❌ No courses found for module {$moduleId}</p>";
} else {
    echo "<p style='color: green;'>✅ Found " . $courses->count() . " courses:</p>";
    foreach ($courses as $course) {
        echo "<ul>";
        echo "<li>Course ID: {$course->subject_id} - {$course->subject_name}</li>";
        
        // 3. Check lessons for this course
        $lessons = Lesson::where('course_id', $course->subject_id)->get();
        echo "<li>Lessons (" . $lessons->count() . "):</li>";
        if ($lessons->isEmpty()) {
            echo "<ul><li style='color: orange;'>⚠️ No lessons found</li></ul>";
        } else {
            echo "<ul>";
            foreach ($lessons as $lesson) {
                echo "<li>Lesson ID: {$lesson->lesson_id} - {$lesson->lesson_name}</li>";
                
                // 4. Check content items for this lesson
                $contentItems = ContentItem::where('lesson_id', $lesson->lesson_id)->get();
                echo "<ul>";
                echo "<li>Content Items (" . $contentItems->count() . "):</li>";
                if ($contentItems->isEmpty()) {
                    echo "<ul><li style='color: orange;'>⚠️ No content items found</li></ul>";
                } else {
                    echo "<ul>";
                    foreach ($contentItems as $item) {
                        echo "<li>Content: {$item->content_title} (Type: {$item->content_type})</li>";
                        if ($item->attachment_path) {
                            echo "<li>Attachment: {$item->attachment_path}</li>";
                        }
                    }
                    echo "</ul>";
                }
                echo "</ul>";
            }
            echo "</ul>";
        }
        echo "</ul>";
    }
}

// 5. Also check direct content items linked to courses (bypass lessons)
echo "<h3>Direct Course Content Items:</h3>";
foreach ($courses as $course) {
    $directContentItems = ContentItem::where('course_id', $course->subject_id)->whereNull('lesson_id')->get();
    if (!$directContentItems->isEmpty()) {
        echo "<p>Course {$course->subject_name} has " . $directContentItems->count() . " direct content items:</p>";
        echo "<ul>";
        foreach ($directContentItems as $item) {
            echo "<li>{$item->content_title} (Type: {$item->content_type})</li>";
        }
        echo "</ul>";
    }
}

// 6. Show all content items for this course regardless of lesson
echo "<h3>All Content Items for Module Courses:</h3>";
foreach ($courses as $course) {
    $allContentItems = ContentItem::where('course_id', $course->subject_id)->get();
    if (!$allContentItems->isEmpty()) {
        echo "<p>Course {$course->subject_name} has " . $allContentItems->count() . " total content items:</p>";
        echo "<ul>";
        foreach ($allContentItems as $item) {
            echo "<li>{$item->content_title} (Type: {$item->content_type}) - Lesson ID: " . ($item->lesson_id ?? 'None') . "</li>";
        }
        echo "</ul>";
    }
}

?>
