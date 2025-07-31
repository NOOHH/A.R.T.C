<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Content Items Test ===\n";

try {
    $count = DB::table('content_items')->count();
    echo "Total content items: $count\n\n";
    
    $items = DB::table('content_items')->select('id', 'content_title', 'content_type')->get();
    
    foreach ($items as $item) {
        echo "ID: {$item->id} - {$item->content_title} ({$item->content_type})\n";
    }
    
    echo "\n=== Testing ID 78 ===\n";
    $content78 = DB::table('content_items')->where('id', 78)->first();
    if ($content78) {
        echo "Content 78 found: {$content78->content_title}\n";
    } else {
        echo "Content 78 NOT found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
