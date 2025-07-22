<?php

// Check content items in database
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ContentItem;

echo "=== CONTENT ITEMS DATABASE TEST ===\n\n";

try {
    // Get all content items
    $contentItems = ContentItem::orderBy('created_at', 'desc')->get();
    
    echo "Total content items: " . $contentItems->count() . "\n\n";
    
    echo "All content items:\n";
    foreach ($contentItems as $item) {
        echo "  ID: {$item->id}\n";
        echo "  Title: {$item->content_title}\n";
        echo "  Type: {$item->content_type}\n";
        echo "  Course ID: {$item->course_id}\n";
        echo "  Attachment Path: " . ($item->attachment_path ?: 'None') . "\n";
        echo "  Created: {$item->created_at}\n";
        echo "  ---\n";
    }
    
    // Get content items with attachments
    $withAttachments = ContentItem::whereNotNull('attachment_path')->get();
    echo "\nContent items with attachments: " . $withAttachments->count() . "\n";
    
    foreach ($withAttachments as $item) {
        echo "  ID: {$item->id} - {$item->content_title} - {$item->attachment_path}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
