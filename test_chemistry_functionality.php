<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Chemistry Course Edit and Archive Functionality\n";
echo "======================================================\n";

// Set up session for testing
session(['logged_in' => true, 'professor_id' => 8, 'user_role' => 'professor']);

// Test the controller methods directly
$controller = new \App\Http\Controllers\Professor\ProfessorModuleController();

// Chemistry course ID: 48
// Lessons 1 content ID: 83

echo "1. Testing getCourseContent for Chemistry course (ID: 48):\n";
try {
    $response = $controller->getCourseContent(48);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        if ($data['success']) {
            echo "✓ getCourseContent works\n";
            echo "- Content items returned: " . count($data['content']) . "\n";
            foreach($data['content'] as $content) {
                echo "  - ID: " . $content['id'] . " | Title: " . $content['content_title'] . "\n";
            }
        } else {
            echo "✗ getCourseContent failed: " . ($data['error'] ?? 'Unknown error') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ getCourseContent error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing editContent for Lessons 1 (ID: 83):\n";
try {
    $response = $controller->editContent(83);
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "✗ editContent redirected (likely error): " . $response->getTargetUrl() . "\n";
    } else {
        echo "✓ editContent works - returned view\n";
    }
} catch (\Exception $e) {
    echo "✗ editContent error: " . $e->getMessage() . "\n";
}

echo "\n3. Testing archiveContent for Lessons 1 (ID: 83):\n";
try {
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->headers->set('Accept', 'application/json');
    
    $response = $controller->archiveContent(83);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        if ($data['success']) {
            echo "✓ archiveContent works\n";
            
            // Unarchive it again for further testing
            $content = \App\Models\ContentItem::find(83);
            $content->update(['is_archived' => false, 'archived_at' => null]);
            echo "✓ Content unarchived again\n";
        } else {
            echo "✗ archiveContent failed: " . $data['message'] . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ archiveContent error: " . $e->getMessage() . "\n";
}

echo "\n4. Testing archiveCourse for Chemistry (ID: 48):\n";
try {
    $response = $controller->archiveCourse(48);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        if ($data['success']) {
            echo "✓ archiveCourse works\n";
            
            // Unarchive it again
            $course = \App\Models\Course::find(48);
            $course->update(['is_archived' => false]);
            echo "✓ Course unarchived again\n";
        } else {
            echo "✗ archiveCourse failed: " . $data['message'] . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ archiveCourse error: " . $e->getMessage() . "\n";
}

echo "\nTesting completed!\n";
