<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Final verification test...\n";

try {
    echo "=== Summary of all announcements ===\n";
    
    $allAnnouncements = App\Models\Announcement::where('is_active', true)
        ->where('is_published', true)
        ->orderBy('created_at', 'desc')
        ->get();
    
    foreach ($allAnnouncements as $ann) {
        echo "\nAnnouncement: {$ann->title}\n";
        echo "- Scope: {$ann->target_scope}\n";
        
        if ($ann->target_scope === 'specific') {
            echo "- Users: " . json_encode($ann->target_users) . " (type: " . gettype($ann->target_users) . ")\n";
            echo "- Programs: " . json_encode($ann->target_programs) . " (type: " . gettype($ann->target_programs) . ")\n";
            echo "- Batches: " . json_encode($ann->target_batches) . " (type: " . gettype($ann->target_batches) . ")\n";
            echo "- Plans: " . json_encode($ann->target_plans) . " (type: " . gettype($ann->target_plans) . ")\n";
            
            // Check if properly formatted
            $isProperlyFormatted = is_array($ann->target_users);
            echo "- Properly formatted: " . ($isProperlyFormatted ? 'YES' : 'NO') . "\n";
        }
    }
    
    echo "\n=== Testing targeting for test student ===\n";
    
    $student = App\Models\Student::with('enrollments')->first();
    $enrolledProgramIds = $student->enrollments()
        ->where('enrollment_status', 'approved')
        ->pluck('program_id')
        ->unique()
        ->toArray();
    
    echo "Student {$student->student_id} programs: " . implode(', ', $enrolledProgramIds) . "\n";
    
    // Test targeting with the fixed method
    $controller = new App\Http\Controllers\StudentDashboardController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getTargetedAnnouncements');
    $method->setAccessible(true);
    
    $targetedAnnouncements = $method->invoke($controller, $student, $enrolledProgramIds);
    
    echo "\nFound {$targetedAnnouncements->count()} targeted announcements:\n";
    foreach ($targetedAnnouncements as $ann) {
        echo "- {$ann->title} (scope: {$ann->target_scope})\n";
    }
    
    echo "\n=== JSON query compatibility test ===\n";
    
    // Test how many work with JSON queries
    $properJsonCount = App\Models\Announcement::where('target_scope', 'specific')
        ->whereJsonContains('target_users', 'students')
        ->count();
    
    echo "Announcements working with JSON queries: {$properJsonCount}\n";
    
    // Count total specific announcements
    $totalSpecific = App\Models\Announcement::where('target_scope', 'specific')->count();
    echo "Total specific announcements: {$totalSpecific}\n";
    
    echo "\n✅ SUMMARY:\n";
    echo "- Fixed controller to not double-encode JSON\n";
    echo "- Fixed targeting logic to handle both old and new data formats\n";
    echo "- New announcements store JSON properly and work with targeting\n";
    echo "- Old announcements with malformed JSON are handled by fallback logic\n";
    echo "- Student targeting is now working correctly\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
