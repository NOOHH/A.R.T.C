<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing announcement creation with proper JSON format...\n";

try {
    // Create a test announcement with proper array data
    $announcement = App\Models\Announcement::create([
        'admin_id' => 1,
        'title' => 'Test Proper JSON Format',
        'content' => 'Testing proper JSON storage and retrieval',
        'description' => 'Test description',
        'type' => 'general',
        'target_scope' => 'specific',
        'target_users' => ['students'], // Pass as array, not JSON string
        'target_programs' => [1, 2],
        'target_batches' => [1],
        'target_plans' => [1, 2],
        'is_published' => true,
        'is_active' => true,
        'publish_date' => now(),
        'expire_date' => now()->addDays(30),
    ]);
    
    echo "✅ Created announcement ID: {$announcement->announcement_id}\n";
    
    // Fresh retrieve to test casting
    $fresh = App\Models\Announcement::find($announcement->announcement_id);
    echo "Raw target_users: " . $fresh->getRawOriginal('target_users') . "\n";
    echo "Casted target_users: " . json_encode($fresh->target_users) . "\n";
    echo "Type: " . gettype($fresh->target_users) . "\n";
    
    if (is_array($fresh->target_users)) {
        echo "✅ Properly cast to array!\n";
        echo "Contains 'students': " . (in_array('students', $fresh->target_users) ? 'YES' : 'NO') . "\n";
    } else {
        echo "❌ Still not cast to array\n";
    }
    
    // Test querying with Laravel method
    echo "\n=== Testing Laravel JSON queries ===\n";
    
    $found = App\Models\Announcement::whereJsonContains('target_users', 'students')->count();
    echo "Laravel whereJsonContains: {$found} found\n";
    
    // Test with Collection filtering (PHP-based)
    $allSpecific = App\Models\Announcement::where('target_scope', 'specific')->get();
    $filtered = $allSpecific->filter(function($ann) {
        return is_array($ann->target_users) && in_array('students', $ann->target_users);
    });
    echo "PHP array filtering: {$filtered->count()} found\n";
    
    echo "\nAll specific announcements:\n";
    foreach ($allSpecific as $ann) {
        echo "- {$ann->title}: " . json_encode($ann->target_users) . " (type: " . gettype($ann->target_users) . ")\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
