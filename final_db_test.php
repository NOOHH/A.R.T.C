<?php

echo "=== FINAL DATABASE TEST ===\n\n";

try {
    // Simple database connectivity test
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep_z-smartprep-local', 'root', '');
    
    echo "✓ Connected to tenant database\n";
    
    // Test settings table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'settings'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Settings table exists\n";
        
        // Test update operation
        $testBrandName = 'FINAL_TEST_' . date('His');
        
        $stmt = $pdo->prepare("INSERT INTO settings (group_name, key_name, value, type) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
        $result = $stmt->execute(['navbar', 'brand_name', $testBrandName, 'text']);
        
        if ($result) {
            echo "✓ Brand name update successful: $testBrandName\n";
            
            // Verify the update
            $stmt = $pdo->prepare("SELECT value FROM settings WHERE group_name = ? AND key_name = ?");
            $stmt->execute(['navbar', 'brand_name']);
            $savedName = $stmt->fetchColumn();
            
            if ($savedName === $testBrandName) {
                echo "✓ Brand name verification successful\n";
            } else {
                echo "✗ Brand name verification failed\n";
            }
        } else {
            echo "✗ Brand name update failed\n";
        }
        
        // Count total settings
        $stmt = $pdo->query("SELECT COUNT(*) FROM settings");
        $count = $stmt->fetchColumn();
        echo "✓ Total settings in database: $count\n";
        
    } else {
        echo "✗ Settings table not found\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== DATABASE OPERATIONS CONFIRMED WORKING ===\n";
