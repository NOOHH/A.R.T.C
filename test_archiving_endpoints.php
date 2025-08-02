<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Archiving API Endpoints\n";
echo "==============================\n";

// Test ContentItem archiving via controller method
try {
    $controller = new \App\Http\Controllers\Professor\ProfessorModuleController();
    
    // Mock a request for content archiving
    $request = new \Illuminate\Http\Request();
    $request->headers->set('Accept', 'application/json');
    
    // Get a content item to test with
    $content = \App\Models\ContentItem::where('is_archived', false)->first();
    if ($content) {
        echo "Testing content archiving for ID: " . $content->id . "\n";
        
        // Enable professor module management if not enabled
        \App\Models\AdminSetting::updateOrCreate([
            'setting_key' => 'professor_module_management_enabled'
        ], [
            'setting_value' => '1'
        ]);
        
        // Set the whitelist to allow all professors 
        \App\Models\AdminSetting::updateOrCreate([
            'setting_key' => 'professor_module_management_whitelist'
        ], [
            'setting_value' => ''
        ]);
        
        // Set session for the controller to work
        session(['logged_in' => true, 'professor_id' => 8, 'user_role' => 'professor']);
        
        echo "✓ Settings configured for testing\n";
        echo "✓ Session configured for professor ID 8\n";
        
        // Test content archiving
        $response = $controller->archiveContent($content->id);
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            if ($data['success']) {
                echo "✓ Content archive API endpoint working\n";
            } else {
                echo "✗ Content archive failed: " . $data['message'] . "\n";
            }
        }
        
        // Reset archive status
        $content->update(['is_archived' => false, 'archived_at' => null]);
        echo "✓ Content archive status reset\n";
    }
    
    // Test Course archiving
    $course = \App\Models\Course::where('is_archived', false)->first();
    if ($course) {
        echo "\nTesting course archiving for ID: " . $course->subject_id . "\n";
        
        $response = $controller->archiveCourse($course->subject_id);
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            if ($data['success']) {
                echo "✓ Course archive API endpoint working\n";
            } else {
                echo "✗ Course archive failed: " . $data['message'] . "\n";
            }
        }
        
        // Reset archive status
        $course->update(['is_archived' => false]);
        echo "✓ Course archive status reset\n";
    }

} catch (\Exception $e) {
    echo "✗ Error testing archiving: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nArchiving tests completed!\n";
