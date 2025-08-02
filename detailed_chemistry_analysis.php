<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Detailed Chemistry Course Content Analysis\n";
echo "=========================================\n";

// Check the actual content data in detail
$content = \App\Models\ContentItem::where('course_id', 48)->get();

echo "All content for Chemistry course (ID: 48):\n";
foreach($content as $item) {
    echo "Content ID: " . $item->id . "\n";
    echo "Title: " . $item->content_title . "\n";
    echo "Description: " . ($item->content_description ?: 'No description') . "\n";
    echo "Type: " . $item->content_type . "\n";
    echo "Is Active: " . ($item->is_active ? 'Yes' : 'No') . "\n";
    echo "Is Archived: " . ($item->is_archived ? 'Yes' : 'No') . "\n";
    echo "Created: " . $item->created_at . "\n";
    echo "Updated: " . $item->updated_at . "\n";
    echo "---\n";
}

echo "\nTesting professor access to this content:\n";

// Check if professor 8 has access to this course
$professor = \App\Models\Professor::find(8);
if ($professor) {
    echo "Professor found: " . $professor->name . "\n";
    
    // Check program access
    $programs = $professor->assignedPrograms()->get();
    echo "Assigned programs: " . $programs->count() . "\n";
    foreach($programs as $program) {
        echo "- Program ID: " . $program->id . " | Name: " . $program->program_name . "\n";
    }
    
    // Check course access through the controller
    $courses = \App\Models\Course::whereIn('program_id', $programs->pluck('id'))->get();
    echo "\nAccessible courses: " . $courses->count() . "\n";
    foreach($courses as $course) {
        echo "- Course ID: " . $course->id . " | Name: " . $course->course_name . " | Program: " . $course->program_id . "\n";
        if ($course->id == 48) {
            echo "  ✓ Chemistry course is accessible\n";
        }
    }
}

echo "\nTesting the actual getCourseContent method:\n";
session(['logged_in' => true, 'professor_id' => 8, 'user_role' => 'professor']);

$controller = new \App\Http\Controllers\Professor\ProfessorModuleController();
$response = $controller->getCourseContent(48);

if ($response instanceof \Illuminate\Http\JsonResponse) {
    $data = $response->getData(true);
    if ($data['success']) {
        echo "✓ Method returns success\n";
        echo "Content items returned: " . count($data['content']) . "\n";
        foreach($data['content'] as $content) {
            echo "- ID: " . $content['id'] . " | Title: " . $content['content_title'] . " | Description: " . ($content['content_description'] ?: 'No description') . "\n";
        }
    } else {
        echo "✗ Method failed: " . $data['error'] . "\n";
    }
} else {
    echo "✗ Unexpected response type\n";
}

echo "\nAnalysis complete!\n";
