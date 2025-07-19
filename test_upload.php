<?php
// Simple test to check content items and file upload directory

// Set up Laravel environment
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Content Items...\n";

try {
    // Test database connection
    $count = \App\Models\ContentItem::count();
    echo "Total ContentItems in database: $count\n";
    
    // Test storage directory
    $storagePath = storage_path('app/public/content');
    echo "Storage path: $storagePath\n";
    echo "Directory exists: " . (is_dir($storagePath) ? "YES" : "NO") . "\n";
    echo "Directory writable: " . (is_writable($storagePath) ? "YES" : "NO") . "\n";
    
    // Create directory if it doesn't exist
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
        echo "Created storage directory\n";
    }
    
    // Test latest content items
    $latest = \App\Models\ContentItem::latest()->take(3)->get(['id', 'content_title', 'content_type', 'attachment_path']);
    echo "\nLatest 3 content items:\n";
    foreach ($latest as $item) {
        echo "ID: {$item->id}, Title: {$item->content_title}, Type: {$item->content_type}, File: {$item->attachment_path}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
?>
