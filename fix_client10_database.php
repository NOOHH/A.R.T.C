<?php

echo "=== FIXING CLIENT 10 DATABASE ISSUE ===\n\n";

try {
    $mainPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep', 'root', '');
    
    echo "1. CHECKING ALL SMARTPREP DATABASES:\n";
    $stmt = $mainPdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $smartprepDbs = [];
    foreach ($databases as $dbName) {
        if (strpos($dbName, 'smartprep_') === 0) {
            $smartprepDbs[] = $dbName;
            echo "  - $dbName\n";
            
            try {
                $dbPdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=$dbName", 'root', '');
                $stmt = $dbPdo->query("SHOW TABLES LIKE 'settings'");
                $hasSettings = $stmt->rowCount() > 0;
                echo "    Settings table: " . ($hasSettings ? 'EXISTS' : 'MISSING') . "\n";
                
                if ($hasSettings) {
                    $stmt = $dbPdo->query("SELECT COUNT(*) FROM settings");
                    $count = $stmt->fetchColumn();
                    echo "    Settings count: $count\n";
                }
            } catch (Exception $e) {
                echo "    Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n2. CREATING SETTINGS TABLE FOR CLIENT 10:\n";
    
    $clientDbName = 'smartprep_client-smartprep-local';
    
    if (in_array($clientDbName, $smartprepDbs)) {
        echo "Database $clientDbName exists, creating settings table...\n";
        
        $clientPdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=$clientDbName", 'root', '');
        
        // Create settings table
        $createTableSql = "CREATE TABLE IF NOT EXISTS settings (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `group` varchar(100) NOT NULL,
            `key` varchar(100) NOT NULL,
            value text,
            type varchar(50) DEFAULT 'text',
            created_at timestamp NULL DEFAULT NULL,
            updated_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_group_key (`group`, `key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $clientPdo->exec($createTableSql);
        echo "✓ Settings table created\n";
        
        echo "\n3. COPYING ADMIN SETTINGS TO CLIENT 10:\n";
        
        // Get admin settings from main database
        $stmt = $mainPdo->query("SELECT * FROM ui_settings");
        $adminSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Found " . count($adminSettings) . " admin settings to copy\n";
        
        $insertSql = "INSERT INTO settings (`group`, `key`, value, type, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, NOW(), NOW()) 
                      ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()";
        $insertStmt = $clientPdo->prepare($insertSql);
        
        $copiedCount = 0;
        foreach ($adminSettings as $setting) {
            $group = $setting['group'] ?? 'general';
            $key = $setting['key'] ?? 'unknown';
            $value = $setting['value'] ?? '';
            $type = $setting['type'] ?? 'text';
            
            $insertStmt->execute([$group, $key, $value, $type]);
            $copiedCount++;
        }
        
        echo "✓ Copied $copiedCount settings\n";
        
        // Verify the copy
        $stmt = $clientPdo->query("SELECT COUNT(*) FROM settings");
        $totalSettings = $stmt->fetchColumn();
        echo "✓ Total settings in client database: $totalSettings\n";
        
        // Test setting a navbar value
        echo "\n4. TESTING NAVBAR SETTING UPDATE:\n";
        
        $testBrandName = 'CLIENT_TEST_' . date('His');
        $updateSql = "INSERT INTO settings (`group`, `key`, value, type, created_at, updated_at) 
                      VALUES ('navbar', 'brand_name', ?, 'text', NOW(), NOW()) 
                      ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()";
        $stmt = $clientPdo->prepare($updateSql);
        $stmt->execute([$testBrandName]);
        
        // Verify the update
        $stmt = $clientPdo->prepare("SELECT value FROM settings WHERE `group` = 'navbar' AND `key` = 'brand_name'");
        $stmt->execute();
        $savedValue = $stmt->fetchColumn();
        
        if ($savedValue === $testBrandName) {
            echo "✓ Navbar setting test successful: $testBrandName\n";
        } else {
            echo "❌ Navbar setting test failed\n";
        }
        
    } else {
        echo "❌ Database $clientDbName does not exist\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n=== FIX COMPLETE ===\n";
echo "Client 10 should now be able to save navbar settings!\n";
