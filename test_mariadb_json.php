<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing MariaDB JSON handling...\n";

try {
    // Test raw SQL with DB facade
    $results = DB::select("SELECT announcement_id, title, target_users, JSON_CONTAINS(target_users, '\"students\"') as contains_students FROM announcements WHERE target_scope = 'specific'");
    
    echo "Raw SQL test results:\n";
    foreach ($results as $result) {
        echo "ID: {$result->announcement_id}, Title: {$result->title}, Target Users: {$result->target_users}, Contains Students: {$result->contains_students}\n";
    }
    
    // Test Laravel JSON queries with different approaches
    echo "\nTesting Laravel whereJsonContains:\n";
    $laravelResults = App\Models\Announcement::where('target_scope', 'specific')
        ->whereJsonContains('target_users', 'students')
        ->get(['announcement_id', 'title', 'target_users']);
    
    echo "Laravel JSON Contains results: " . $laravelResults->count() . " found\n";
    
    // Test alternative Laravel approach
    echo "\nTesting Laravel whereRaw:\n";
    $rawResults = App\Models\Announcement::where('target_scope', 'specific')
        ->whereRaw("JSON_CONTAINS(target_users, '\"students\"')")
        ->get(['announcement_id', 'title', 'target_users']);
    
    echo "Laravel whereRaw results: " . $rawResults->count() . " found\n";
    
    // Test simple LIKE approach as fallback
    echo "\nTesting simple LIKE approach:\n";
    $likeResults = App\Models\Announcement::where('target_scope', 'specific')
        ->where('target_users', 'LIKE', '%"students"%')
        ->get(['announcement_id', 'title', 'target_users']);
    
    echo "LIKE approach results: " . $likeResults->count() . " found\n";
    foreach ($likeResults as $result) {
        echo "- {$result->title} (target_users: {$result->target_users})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
