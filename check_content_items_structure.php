<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\ContentItem;

echo "=== CONTENT ITEMS TABLE STRUCTURE ===\n";

try {
    // Check table structure
    $columns = DB::select('SHOW COLUMNS FROM content_items');
    
    echo "Columns in content_items table:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n=== CHECK FOR attachment_path COLUMN ===\n";
    $hasAttachmentPath = false;
    foreach ($columns as $column) {
        if ($column->Field === 'attachment_path') {
            $hasAttachmentPath = true;
            echo "✅ attachment_path column EXISTS\n";
            echo "   Type: {$column->Type}\n";
            echo "   Null: {$column->Null}\n";
            echo "   Default: {$column->Default}\n";
            break;
        }
    }
    
    if (!$hasAttachmentPath) {
        echo "❌ attachment_path column DOES NOT EXIST\n";
    }
    
    echo "\n=== SAMPLE CONTENT ITEMS ===\n";
    $items = DB::select('SELECT id, content_title, attachment_path FROM content_items LIMIT 5');
    foreach ($items as $item) {
        echo "ID: {$item->id} | Title: {$item->content_title} | Attachment: " . ($item->attachment_path ?: 'NULL') . "\n";
    }
    
    echo "\n=== CONTENT ITEMS WITH NULL attachment_path ===\n";
    $nullItems = DB::select('SELECT COUNT(*) as count FROM content_items WHERE attachment_path IS NULL');
    echo "Items with NULL attachment_path: " . $nullItems[0]->count . "\n";
    
    $nonNullItems = DB::select('SELECT COUNT(*) as count FROM content_items WHERE attachment_path IS NOT NULL');
    echo "Items with attachment_path: " . $nonNullItems[0]->count . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
