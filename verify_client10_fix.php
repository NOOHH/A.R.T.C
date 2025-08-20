<?php

echo "=== VERIFYING CLIENT 10 DATABASE FIX ===\n\n";

try {
    $mainPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep', 'root', '');
    $clientPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep_client-smartprep-local', 'root', '');
    
    echo "1. CHECKING SETTINGS TABLE STRUCTURE:\n";
    
    // Check table structure
    $stmt = $clientPdo->query("DESCRIBE settings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "  {$column['Field']}: {$column['Type']}\n";
    }
    
    echo "\n2. CHECKING CURRENT SETTINGS:\n";
    
    $stmt = $clientPdo->query("SELECT * FROM settings LIMIT 10");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($settings as $setting) {
        echo "  [{$setting['group']}] {$setting['key']} = {$setting['value']}\n";
    }
    
    echo "\n3. MANUALLY COPYING ADMIN SETTINGS:\n";
    
    // Get admin settings with better structure
    $stmt = $mainPdo->query("SELECT `group`, `key`, value, type FROM ui_settings");
    $adminSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($adminSettings) . " admin settings\n";
    
    // Clear existing settings first
    $clientPdo->exec("DELETE FROM settings");
    echo "Cleared existing settings\n";
    
    // Copy each setting individually
    $insertSql = "INSERT INTO settings (`group`, `key`, value, type, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
    $insertStmt = $clientPdo->prepare($insertSql);
    
    $copied = 0;
    foreach ($adminSettings as $setting) {
        try {
            $insertStmt->execute([
                $setting['group'],
                $setting['key'], 
                $setting['value'],
                $setting['type']
            ]);
            $copied++;
        } catch (Exception $e) {
            echo "  Error copying {$setting['group']}.{$setting['key']}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Successfully copied $copied settings\n";
    
    // Verify final count
    $stmt = $clientPdo->query("SELECT COUNT(*) FROM settings");
    $finalCount = $stmt->fetchColumn();
    echo "Final settings count: $finalCount\n";
    
    echo "\n4. TESTING SPECIFIC NAVBAR SETTINGS:\n";
    
    $stmt = $clientPdo->query("SELECT * FROM settings WHERE `group` = 'navbar'");
    $navbarSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($navbarSettings) > 0) {
        echo "Found " . count($navbarSettings) . " navbar settings:\n";
        foreach ($navbarSettings as $setting) {
            echo "  {$setting['key']}: {$setting['value']}\n";
        }
    } else {
        echo "No navbar settings found - adding default ones\n";
        
        // Add some default navbar settings
        $defaultNavbarSettings = [
            ['key' => 'brand_name', 'value' => 'CLIENT'],
            ['key' => 'logo_url', 'value' => ''],
            ['key' => 'background_color', 'value' => '#ffffff'],
            ['key' => 'text_color', 'value' => '#000000']
        ];
        
        foreach ($defaultNavbarSettings as $setting) {
            $insertStmt->execute(['navbar', $setting['key'], $setting['value'], 'text']);
            echo "  Added navbar.{$setting['key']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
