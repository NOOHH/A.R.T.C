<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing fixed announcement creation and targeting...\n";

try {
    // Simulate the same data that would come from the admin form
    $requestData = [
        'title' => 'Test Fixed Announcement',
        'content' => 'This announcement should work with the fixed JSON handling',
        'description' => 'Testing fixed JSON storage',
        'type' => 'general',
        'target_scope' => 'specific',
        'target_users' => ['students'], // This should be an array from the form
        'target_programs' => [1, 2],
        'target_batches' => [1],
        'target_plans' => ['full', 'modular'],
        'is_published' => true,
        'video_link' => null,
        'publish_date' => now(),
        'expire_date' => now()->addDays(30)
    ];
    
    // Create announcement the same way the controller would (without manual json_encode)
    $announcement = new App\Models\Announcement();
    $announcement->admin_id = 1;
    $announcement->title = $requestData['title'];
    $announcement->content = $requestData['content'];
    $announcement->description = $requestData['description'];
    $announcement->type = $requestData['type'];
    $announcement->target_scope = $requestData['target_scope'];
    $announcement->video_link = $requestData['video_link'];
    $announcement->is_published = $requestData['is_published'];
    $announcement->is_active = true;
    
    // Handle targeting with the fixed approach (no manual json_encode)
    if ($requestData['target_scope'] === 'specific') {
        $announcement->target_users = $requestData['target_users'] ?: null;
        $announcement->target_programs = $requestData['target_programs'] ?: null;
        $announcement->target_batches = $requestData['target_batches'] ?: null;
        $announcement->target_plans = $requestData['target_plans'] ?: null;
    }
    
    $announcement->publish_date = $requestData['publish_date'];
    $announcement->expire_date = $requestData['expire_date'];
    $announcement->save();
    
    echo "✅ Created announcement ID: {$announcement->announcement_id}\n";
    
    // Test the data format
    $fresh = App\Models\Announcement::find($announcement->announcement_id);
    echo "Raw target_users: " . $fresh->getRawOriginal('target_users') . "\n";
    echo "Casted target_users: " . json_encode($fresh->target_users) . "\n";
    echo "Type: " . gettype($fresh->target_users) . "\n";
    
    // Test Laravel JSON queries
    echo "\n=== Testing Laravel JSON queries ===\n";
    
    $found = App\Models\Announcement::whereJsonContains('target_users', 'students')->count();
    echo "Laravel whereJsonContains found: {$found} announcements\n";
    
    // Test the StudentDashboardController logic
    echo "\n=== Testing student targeting logic ===\n";
    
    // Get a test student
    $student = App\Models\Student::with('enrollments')->first();
    if ($student) {
        echo "Testing with student ID: {$student->student_id}\n";
        
        // Get enrolled program IDs
        $enrolledProgramIds = $student->enrollments()
            ->where('enrollment_status', 'approved')
            ->pluck('program_id')
            ->unique()
            ->toArray();
        
        echo "Student enrolled programs: " . implode(', ', $enrolledProgramIds) . "\n";
        
        // Test the targeting method by accessing the controller
        $controller = new App\Http\Controllers\StudentDashboardController();
        
        // Use reflection to call private method
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('getTargetedAnnouncements');
        $method->setAccessible(true);
        
        $targetedAnnouncements = $method->invoke($controller, $student, $enrolledProgramIds);
        
        echo "Found {$targetedAnnouncements->count()} targeted announcements\n";
        foreach ($targetedAnnouncements as $ann) {
            echo "- {$ann->title} (scope: {$ann->target_scope})\n";
        }
    } else {
        echo "No student found for testing\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
