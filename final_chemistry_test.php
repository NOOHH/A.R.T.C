<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Final Chemistry Course Fix\n";
echo "==================================\n";

// Set up session for testing
session(['logged_in' => true, 'professor_id' => 8, 'user_role' => 'professor']);

// Test the controller methods directly
$controller = new \App\Http\Controllers\Professor\ProfessorModuleController();

echo "1. Testing getCourseContent for Chemistry course (ID: 48):\n";
try {
    $response = $controller->getCourseContent(48);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        if (isset($data['success']) && $data['success']) {
            echo "✓ getCourseContent works perfectly!\n";
            echo "- Course: " . $data['course']['subject_name'] . "\n";
            echo "- Content items returned: " . count($data['content']) . "\n";
            foreach($data['content'] as $content) {
                echo "  - ID: " . $content['id'] . " | Title: " . $content['content_title'] . " | Description: " . ($content['content_description'] ?: 'No description') . "\n";
            }
        } else {
            echo "✗ getCourseContent failed: " . ($data['error'] ?? 'Unknown error') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ getCourseContent error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing all edit/archive functionality:\n";

// Test edit content
echo "Testing editContent:\n";
try {
    $response = $controller->editContent(83);
    if ($response instanceof \Illuminate\View\View) {
        echo "✓ editContent returns view successfully\n";
    } else {
        echo "✗ editContent unexpected response\n";
    }
} catch (\Exception $e) {
    echo "✗ editContent error: " . $e->getMessage() . "\n";
}

// Test archive content
echo "Testing archiveContent:\n";
try {
    $response = $controller->archiveContent(83);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        if ($data['success']) {
            echo "✓ archiveContent works\n";
            
            // Unarchive it again
            $content = \App\Models\ContentItem::find(83);
            $content->update(['is_archived' => false, 'archived_at' => null]);
            echo "✓ Content unarchived for continued use\n";
        } else {
            echo "✗ archiveContent failed: " . $data['message'] . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ archiveContent error: " . $e->getMessage() . "\n";
}

// Test archive course
echo "Testing archiveCourse:\n";
try {
    $response = $controller->archiveCourse(48);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        if ($data['success']) {
            echo "✓ archiveCourse works\n";
            
            // Unarchive it again
            $course = \App\Models\Course::find(48);
            $course->update(['is_archived' => false]);
            echo "✓ Course unarchived for continued use\n";
        } else {
            echo "✗ archiveCourse failed: " . $data['message'] . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ archiveCourse error: " . $e->getMessage() . "\n";
}

echo "\n🎉 All Chemistry course functionality should now work!\n";
echo "The edit and archive buttons should work properly in the web interface.\n";
