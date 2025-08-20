<?php

echo "=== CHECKING DATABASE STRUCTURE ===\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep_z-smartprep-local', 'root', '');
    
    // Check actual table structure
    $stmt = $pdo->query("DESCRIBE settings");
    echo "Settings table structure:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']} ({$row['Type']})\n";
    }
    
    // Test with correct column names
    echo "\nTesting with correct column names:\n";
    
    $testBrandName = 'FINAL_CORRECTED_' . date('His');
    
    $stmt = $pdo->prepare("INSERT INTO settings (`group`, `key`, value, type) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
    $result = $stmt->execute(['navbar', 'brand_name', $testBrandName, 'text']);
    
    if ($result) {
        echo "✓ Brand name update successful: $testBrandName\n";
        
        // Verify the update
        $stmt = $pdo->prepare("SELECT value FROM settings WHERE `group` = ? AND `key` = ?");
        $stmt->execute(['navbar', 'brand_name']);
        $savedName = $stmt->fetchColumn();
        
        if ($savedName === $testBrandName) {
            echo "✓ Brand name verification successful\n";
        } else {
            echo "✗ Brand name verification failed. Saved: $savedName\n";
        }
    } else {
        echo "✗ Brand name update failed\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== DATABASE OPERATIONS VERIFIED ===\n";
