<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Creating announcement targeted to actual student programs...\n";

try {
    // Get student's actual data
    $student = App\Models\Student::with('enrollments')->first();
    $enrolledProgramIds = $student->enrollments()
        ->where('enrollment_status', 'approved')
        ->pluck('program_id')
        ->unique()
        ->toArray();
    
    $enrollments = $student->enrollments()->where('enrollment_status', 'approved')->get();
    $batchIds = $enrollments->whereNotNull('batch_id')->pluck('batch_id')->unique()->toArray();
    $enrollmentTypes = $enrollments->pluck('enrollment_type')->unique()->toArray();
    
    echo "Student data:\n";
    echo "- Programs: " . implode(', ', $enrolledProgramIds) . "\n";
    echo "- Batches: " . implode(', ', $batchIds) . "\n";
    echo "- Plans: " . implode(', ', $enrollmentTypes) . "\n";
    
    // Create announcement targeting this specific student
    $announcement = App\Models\Announcement::create([
        'admin_id' => 1,
        'title' => 'Test Announcement for Actual Student',
        'content' => 'This should appear for the test student',
        'description' => 'Testing with real student data',
        'type' => 'general',
        'target_scope' => 'specific',
        'target_users' => ['students'],
        'target_programs' => $enrolledProgramIds, // Use actual programs
        'target_batches' => !empty($batchIds) ? $batchIds : null,
        'target_plans' => array_map(function($type) {
            return strtolower($type);
        }, $enrollmentTypes),
        'is_published' => true,
        'is_active' => true,
        'publish_date' => now(),
        'expire_date' => now()->addDays(30),
    ]);
    
    echo "\n✅ Created targeted announcement ID: {$announcement->announcement_id}\n";
    echo "Targeting:\n";
    echo "- Users: " . json_encode($announcement->target_users) . "\n";
    echo "- Programs: " . json_encode($announcement->target_programs) . "\n";
    echo "- Batches: " . json_encode($announcement->target_batches) . "\n";
    echo "- Plans: " . json_encode($announcement->target_plans) . "\n";
    
    // Test the StudentDashboardController logic
    echo "\n=== Testing targeting logic ===\n";
    
    $controller = new App\Http\Controllers\StudentDashboardController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getTargetedAnnouncements');
    $method->setAccessible(true);
    
    $targetedAnnouncements = $method->invoke($controller, $student, $enrolledProgramIds);
    
    echo "Found {$targetedAnnouncements->count()} targeted announcements\n";
    foreach ($targetedAnnouncements as $ann) {
        echo "- {$ann->title} (scope: {$ann->target_scope})\n";
    }
    
    // Also test direct query
    echo "\n=== Testing direct query ===\n";
    
    $directQuery = App\Models\Announcement::where('target_scope', 'specific')
        ->where('is_active', true)
        ->where('is_published', true)
        ->whereJsonContains('target_users', 'students');
    
    if (!empty($enrolledProgramIds)) {
        $directQuery->where(function($q) use ($enrolledProgramIds) {
            $q->whereNull('target_programs');
            foreach ($enrolledProgramIds as $programId) {
                $q->orWhereJsonContains('target_programs', $programId);
            }
        });
    }
    
    $directResults = $directQuery->get();
    echo "Direct query found: {$directResults->count()} announcements\n";
    foreach ($directResults as $ann) {
        echo "- {$ann->title}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
