<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing announcement creation with specific targeting...\n";

try {
    $announcement = new App\Models\Announcement();
    $announcement->admin_id = 1;
    $announcement->title = 'Test Specific Announcement';
    $announcement->content = 'This is a test announcement for students only';
    $announcement->type = 'general';
    $announcement->target_scope = 'specific';
    $announcement->target_users = json_encode(['students']);
    $announcement->is_published = true;
    $announcement->is_active = true;
    $announcement->save();
    
    echo "✅ Announcement created successfully with ID: " . $announcement->announcement_id . "\n";
    
    // Test targeting query
    $students = App\Models\Student::with('enrollments')->take(1)->get();
    if ($students->count() > 0) {
        $student = $students->first();
        $enrolledPrograms = $student->enrollments()
            ->where('enrollment_status', 'approved')
            ->pluck('program_id')
            ->toArray();
            
        echo "Testing targeting for student ID: " . $student->student_id . "\n";
        echo "Student enrolled programs: " . implode(', ', $enrolledPrograms) . "\n";
        
        $controller = new App\Http\Controllers\StudentDashboardController();
        $method = new ReflectionMethod($controller, 'getTargetedAnnouncements');
        $method->setAccessible(true);
        
        $announcements = $method->invoke($controller, $student, $enrolledPrograms);
        echo "Found " . $announcements->count() . " targeted announcements\n";
        
        foreach ($announcements as $ann) {
            echo "- " . $ann->title . " (scope: " . $ann->target_scope . ")\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
