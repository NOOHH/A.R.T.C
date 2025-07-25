<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing database fixes...\n";

try {
    // Test 1: Check if batch validation works
    echo "\n=== Test 1: Batch validation ===\n";
    
    $batches = DB::table('student_batches')->select('batch_id', 'batch_name')->limit(3)->get();
    echo "Found " . $batches->count() . " batches in student_batches table:\n";
    foreach ($batches as $batch) {
        echo "- Batch {$batch->batch_id}: {$batch->batch_name}\n";
    }
    
    // Test 2: Check announcement data types
    echo "\n=== Test 2: Announcement data types ===\n";
    
    $announcements = App\Models\Announcement::where('target_scope', 'specific')->limit(3)->get();
    echo "Found " . $announcements->count() . " specific announcements:\n";
    
    foreach ($announcements as $ann) {
        echo "\nAnnouncement: {$ann->title}\n";
        echo "- target_users type: " . gettype($ann->target_users) . "\n";
        echo "- target_programs type: " . gettype($ann->target_programs) . "\n";
        echo "- target_batches type: " . gettype($ann->target_batches) . "\n";
        echo "- target_plans type: " . gettype($ann->target_plans) . "\n";
        
        // Test the hybrid approach in views
        $targetUsers = [];
        if (is_array($ann->target_users)) {
            $targetUsers = $ann->target_users;
        } elseif (is_string($ann->target_users)) {
            $targetUsers = json_decode($ann->target_users, true) ?: [];
        }
        
        echo "- Parsed target_users: " . json_encode($targetUsers) . "\n";
    }
    
    echo "\n✅ All tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
