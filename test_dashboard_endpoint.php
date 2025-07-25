<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing actual student dashboard endpoint...\n";

try {
    // Simulate being logged in as the test student
    $student = App\Models\Student::first();
    
    if (!$student) {
        echo "No student found for testing\n";
        exit;
    }
    
    echo "Testing as student: {$student->student_id}\n";
    
    // Create a request to the dashboard controller
    $request = new Illuminate\Http\Request();
    $request->setUserResolver(function() use ($student) {
        return $student;
    });
    
    // Mock authentication
    \Auth::shouldReceive('guard->user')->andReturn($student);
    \Auth::shouldReceive('user')->andReturn($student);
    \Auth::shouldReceive('id')->andReturn($student->student_id);
    
    $controller = new App\Http\Controllers\StudentDashboardController();
    
    echo "\n=== Calling dashboard index method ===\n";
    
    // Call the index method which should return announcements
    try {
        $response = $controller->index($request);
        
        if ($response instanceof Illuminate\View\View) {
            $data = $response->getData();
            
            if (isset($data['announcements'])) {
                $announcements = $data['announcements'];
                echo "Dashboard found {$announcements->count()} announcements:\n";
                
                foreach ($announcements as $announcement) {
                    echo "- {$announcement->title} (scope: {$announcement->target_scope})\n";
                }
            } else {
                echo "No announcements key in view data\n";
                echo "Available keys: " . implode(', ', array_keys($data)) . "\n";
            }
        } else {
            echo "Response is not a view\n";
            echo "Response type: " . get_class($response) . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error calling dashboard index: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Testing announcements directly ===\n";
    
    // Get announcements directly
    $allAnnouncements = App\Models\Announcement::where('is_active', true)
        ->where('is_published', true)
        ->orderBy('created_at', 'desc')
        ->get();
    
    echo "All active announcements:\n";
    foreach ($allAnnouncements as $ann) {
        echo "- {$ann->title} (scope: {$ann->target_scope})\n";
        if ($ann->target_scope === 'specific') {
            echo "  Users: " . json_encode($ann->target_users) . "\n";
            echo "  Programs: " . json_encode($ann->target_programs) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
