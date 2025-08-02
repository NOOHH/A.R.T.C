<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking ContentItem table structure\n";
echo "===================================\n";

try {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('content_items');
    echo "ContentItem table columns:\n";
    foreach($columns as $column) {
        echo "- " . $column . "\n";
    }
    
    echo "\nChecking if archiving columns exist:\n";
    echo "- is_archived: " . (in_array('is_archived', $columns) ? 'YES' : 'NO') . "\n";
    echo "- archived_at: " . (in_array('archived_at', $columns) ? 'YES' : 'NO') . "\n";
    echo "- archived_by_professor_id: " . (in_array('archived_by_professor_id', $columns) ? 'YES' : 'NO') . "\n";
    
    // Test creating a sample content item
    $content = \App\Models\ContentItem::first();
    if ($content) {
        echo "\nSample content item:\n";
        echo "- ID: " . $content->id . "\n";
        echo "- Title: " . $content->content_title . "\n";
        echo "- Type: " . $content->content_type . "\n";
        echo "- Archived: " . ($content->is_archived ? 'YES' : 'NO') . "\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
