<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing JSON queries for announcements...\n";

try {
    // Test 1: Get all specific announcements
    $specific = App\Models\Announcement::where('target_scope', 'specific')->get();
    echo "Found " . $specific->count() . " specific announcements\n";
    
    // Test 2: Test JSON contains for students
    $studentAnnouncements = App\Models\Announcement::where('target_scope', 'specific')
        ->whereJsonContains('target_users', 'students')
        ->get();
    echo "Found " . $studentAnnouncements->count() . " announcements targeting students\n";
    
    // Test 3: Test with null target_users
    $nullUsers = App\Models\Announcement::where('target_scope', 'specific')
        ->whereNull('target_users')
        ->get();
    echo "Found " . $nullUsers->count() . " announcements with null target_users\n";
    
    // Test 4: Test combined query
    $combined = App\Models\Announcement::where('target_scope', 'specific')
        ->where(function($q) {
            $q->whereNull('target_users')
              ->orWhereJsonContains('target_users', 'students');
        })
        ->get();
    echo "Found " . $combined->count() . " announcements with combined user targeting\n";
    
    // Test 5: Full query like in dashboard
    $fullQuery = App\Models\Announcement::where('is_active', true)
        ->where('is_published', true)
        ->where('target_scope', 'specific')
        ->where(function($targetingQuery) {
            $targetingQuery->where(function($userTypeQuery) {
                $userTypeQuery->whereNull('target_users')
                             ->orWhereJsonContains('target_users', 'students');
            });
        })
        ->get();
    echo "Found " . $fullQuery->count() . " announcements with full query\n";
    
    foreach ($fullQuery as $ann) {
        echo "- " . $ann->title . " (target_users: " . $ann->target_users . ")\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
