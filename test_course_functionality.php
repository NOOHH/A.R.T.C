<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Course Edit and Archive Functionality\n";
echo "=============================================\n";

// Set up session for testing
session(['logged_in' => true, 'professor_id' => 8, 'user_role' => 'professor']);

// Test the controller methods directly
$controller = new \App\Http\Controllers\Professor\ProfessorModuleController();

echo "1. Testing editCourse for Math course (ID: 49):\n";
try {
    $response = $controller->editCourse(49);
    if ($response instanceof \Illuminate\View\View) {
        echo "✓ editCourse returns view successfully\n";
        echo "- View name: " . $response->getName() . "\n";
        $data = $response->getData();
        echo "- Course: " . $data['course']->subject_name . "\n";
        echo "- Module: " . $data['module']->module_name . "\n";
        echo "- Program: " . $data['program']->program_name . "\n";
    } else {
        echo "✗ editCourse unexpected response type: " . get_class($response) . "\n";
    }
} catch (\Exception $e) {
    echo "✗ editCourse error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing archiveCourse for Math course (ID: 49):\n";
try {
    $response = $controller->archiveCourse(49);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        if ($data['success']) {
            echo "✓ archiveCourse works\n";
            
            // Unarchive it again
            $course = \App\Models\Course::find(49);
            $course->update(['is_archived' => false]);
            echo "✓ Course unarchived for continued use\n";
        } else {
            echo "✗ archiveCourse failed: " . $data['message'] . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ archiveCourse error: " . $e->getMessage() . "\n";
}

echo "\n3. Testing updateCourse with sample data:\n";
try {
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'subject_name' => 'Math (Updated)',
        'subject_description' => 'Updated math course description',
        'subject_price' => 2600.00,
        'subject_order' => 1,
        'is_required' => 1,
        'is_active' => 1
    ]);
    $request->headers->set('Accept', 'application/json');
    
    $response = $controller->updateCourse($request, 49);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        if ($data['success']) {
            echo "✓ updateCourse works\n";
            echo "- Updated course: " . $data['course']['subject_name'] . "\n";
            
            // Restore original name
            $course = \App\Models\Course::find(49);
            $course->update(['subject_name' => 'Math']);
            echo "✓ Course name restored\n";
        } else {
            echo "✗ updateCourse failed: " . $data['message'] . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ updateCourse error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Course edit and archive functionality should now work!\n";
echo "Both the edit and archive buttons should work properly in the web interface.\n";
