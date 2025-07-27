<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Analyzing stored JSON data format...\n";

try {
    // Get raw data
    $announcements = App\Models\Announcement::where('target_scope', 'specific')->get();
    
    foreach ($announcements as $announcement) {
        echo "\nAnnouncement: {$announcement->title}\n";
        echo "Raw target_users: {$announcement->getRawOriginal('target_users')}\n";
        echo "Casted target_users: " . json_encode($announcement->target_users) . "\n";
        echo "Type: " . gettype($announcement->target_users) . "\n";
        
        if (is_array($announcement->target_users)) {
            echo "Array contents: " . implode(', ', $announcement->target_users) . "\n";
            echo "Contains 'students': " . (in_array('students', $announcement->target_users) ? 'YES' : 'NO') . "\n";
        }
    }
    
    echo "\n=== Testing corrected queries ===\n";
    
    // Test with corrected JSON format
    $results1 = App\Models\Announcement::where('target_scope', 'specific')
        ->whereRaw("JSON_CONTAINS(target_users, '\"students\"')")
        ->get();
    echo "Direct JSON_CONTAINS with escaped quotes: " . $results1->count() . "\n";
    
    // Test with LIKE for the escaped format
    $results2 = App\Models\Announcement::where('target_scope', 'specific')
        ->where('target_users', 'LIKE', '%\"students\"%')
        ->get();
    echo "LIKE with escaped quotes: " . $results2->count() . "\n";
    
    // Test Laravel cast behavior
    $results3 = App\Models\Announcement::where('target_scope', 'specific')
        ->get()
        ->filter(function($announcement) {
            return is_array($announcement->target_users) && in_array('students', $announcement->target_users);
        });
    echo "PHP array filtering after casting: " . $results3->count() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
