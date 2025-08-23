<?php

try {
    // Test database connection and check if professors table exists
    echo "Testing Database Connection and Professor Table:\n";
    echo "===============================================\n\n";

    $pdo = new PDO('mysql:host=localhost;dbname=smartprep', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful\n\n";
    
    // Check if professors table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'professors'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✅ Professors table exists\n";
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE professors");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nTable structure:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']})\n";
        }
        
        // Check row count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM professors");
        $count = $stmt->fetch()['count'];
        echo "\nRows in table: $count\n";
        
    } else {
        echo "❌ Professors table does NOT exist\n";
        
        // Show all tables
        echo "\nExisting tables:\n";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "\n";
}
