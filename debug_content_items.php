<?php
// Debug script to check content_items table
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc_db', 'root', '');
    
    echo "=== LATEST CONTENT ITEMS ===\n";
    $stmt = $pdo->query('SELECT id, content_title, attachment_path, content_type, created_at FROM content_items ORDER BY created_at DESC LIMIT 10');
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($items as $item) {
        echo "ID: {$item['id']}\n";
        echo "Title: {$item['content_title']}\n";
        echo "Path: " . ($item['attachment_path'] ?: 'NULL') . "\n";
        echo "Type: {$item['content_type']}\n";
        echo "Created: {$item['created_at']}\n";
        echo "---\n";
    }
    
    echo "\n=== TABLE STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE content_items');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "{$column['Field']} | {$column['Type']} | {$column['Null']} | {$column['Default']}\n";
    }
    
    echo "\n=== FILES IN STORAGE ===\n";
    $contentDir = __DIR__ . '/storage/app/public/content';
    if (is_dir($contentDir)) {
        $files = scandir($contentDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $fullPath = $contentDir . '/' . $file;
                echo "File: $file | Size: " . filesize($fullPath) . " bytes\n";
            }
        }
    } else {
        echo "Content directory does not exist: $contentDir\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
