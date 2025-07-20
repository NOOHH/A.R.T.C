<?php

// Test StudentDashboardController getModuleCourses functionality
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StudentDashboardController;

echo "Testing StudentDashboardController getModuleCourses...\n\n";

try {
    $controller = new StudentDashboardController();
    
    // Test with a module ID that exists
    echo "1. Testing getModuleCourses for module 40:\n";
    $response = $controller->getModuleCourses(40);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✓ Module courses retrieved successfully\n";
        echo "   - Found " . count($data['courses']) . " courses\n";
        echo "   - Module: " . $data['module']['name'] . " (ID: " . $data['module']['id'] . ")\n";
        
        // Show first course if available
        if (count($data['courses']) > 0) {
            $firstCourse = $data['courses'][0];
            echo "   - First course: " . $firstCourse['course_name'] . " (ID: " . $firstCourse['course_id'] . ")\n";
            echo "   - Content items: " . count($firstCourse['direct_content_items']) . "\n";
        }
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }
    
    echo "\n✅ Student module courses test completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
