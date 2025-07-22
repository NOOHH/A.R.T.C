<?php

// Simple test to debug ContentItem attachment_path issue
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ContentItem;

echo "=== CONTENT ITEM ATTACHMENT PATH TEST ===\n";

// Test 1: Create a simple content item with attachment_path
$testData = [
    'content_title' => 'Test Content Item',
    'content_description' => 'Testing attachment_path',
    'course_id' => 37, // Using existing course from your test
    'content_type' => 'lesson',
    'content_data' => ['test' => 'data'],
    'attachment_path' => 'content/test_file.pdf', // Test path
    'is_required' => true,
    'is_active' => true,
];

echo "Creating content item with data:\n";
print_r($testData);

try {
    $contentItem = ContentItem::create($testData);
    echo "\n✅ Content item created successfully!\n";
    echo "ID: " . $contentItem->id . "\n";
    echo "Title: " . $contentItem->content_title . "\n";
    echo "Attachment Path (from object): " . ($contentItem->attachment_path ?: 'NULL') . "\n";
    
    // Fetch fresh from database
    $fresh = ContentItem::find($contentItem->id);
    echo "Attachment Path (fresh fetch): " . ($fresh->attachment_path ?: 'NULL') . "\n";
    
    // Check fillable fields
    echo "\nFillable fields:\n";
    print_r($contentItem->getFillable());
    
    // Raw database query
    echo "\nRaw database query:\n";
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc_db', 'root', '');
    $stmt = $pdo->prepare('SELECT attachment_path FROM content_items WHERE id = ?');
    $stmt->execute([$contentItem->id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Raw attachment_path: " . ($result['attachment_path'] ?: 'NULL') . "\n";
    
    // Clean up
    $contentItem->delete();
    echo "\n🧹 Test record deleted.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
