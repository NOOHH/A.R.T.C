<?php

echo "=== CHECKING DATABASE TABLES ===" . PHP_EOL;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    echo "1. Tables with 'content' in the name:" . PHP_EOL;
    $stmt = $pdo->query("SHOW TABLES LIKE '%content%'");
    $contentTables = [];
    while($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "   {$row[0]}" . PHP_EOL;
        $contentTables[] = $row[0];
    }
    
    echo PHP_EOL . "2. All tables in database:" . PHP_EOL;
    $stmt = $pdo->query("SHOW TABLES");
    $tables = [];
    while($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    // Look for tables that might store content
    echo "   Possible content-related tables:" . PHP_EOL;
    $contentRelatedKeywords = ['content', 'lesson', 'material', 'course', 'subject', 'module', 'resource'];
    foreach ($tables as $table) {
        foreach ($contentRelatedKeywords as $keyword) {
            if (stripos($table, $keyword) !== false) {
                echo "   - {$table}" . PHP_EOL;
                break;
            }
        }
    }
    
    // For each content-related table, check its structure
    echo PHP_EOL . "3. Structure of content-related tables:" . PHP_EOL;
    foreach ($contentTables as $table) {
        echo "   Table: {$table}" . PHP_EOL;
        $stmt = $pdo->query("DESCRIBE {$table}");
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "   - {$row['Field']} ({$row['Type']})" . PHP_EOL;
        }
        echo PHP_EOL;
    }
    
    // Check for content completion data
    echo "4. Checking content_completions data:" . PHP_EOL;
    if (in_array('content_completions', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM content_completions");
        $count = $stmt->fetchColumn();
        echo "   Total content_completions records: {$count}" . PHP_EOL;
        
        $stmt = $pdo->query("SELECT DISTINCT content_id FROM content_completions LIMIT 10");
        $contentIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($contentIds)) {
            echo "   Sample content_id values:" . PHP_EOL;
            foreach ($contentIds as $id) {
                echo "   - {$id}" . PHP_EOL;
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>
