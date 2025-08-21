<?php

echo "=== CHECKING UI_SETTINGS TABLE STRUCTURE ===\n\n";

try {
    $mainPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep', 'root', '');
    
    echo "1. UI_SETTINGS TABLE STRUCTURE:\n";
    $stmt = $mainPdo->query("DESCRIBE ui_settings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "  {$column['Field']}: {$column['Type']}\n";
    }
    
    echo "\n2. SAMPLE UI_SETTINGS DATA:\n";
    $stmt = $mainPdo->query("SELECT * FROM ui_settings LIMIT 10");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($settings as $setting) {
        echo "  " . json_encode($setting) . "\n";
    }
    
    echo "\n3. CHECKING WORKING Z.SMARTPREP.LOCAL SETTINGS:\n";
    
    $workingPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep_z-smartprep-local', 'root', '');
    $stmt = $workingPdo->query("SELECT * FROM settings WHERE `group` = 'navbar' LIMIT 5");
    $workingSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Working navbar settings:\n";
    foreach ($workingSettings as $setting) {
        echo "  [{$setting['group']}] {$setting['key']} = {$setting['value']}\n";
    }
    
    echo "\n4. COPYING FROM WORKING DATABASE:\n";
    
    $clientPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep_client-smartprep-local', 'root', '');
    
    // Clear existing settings
    $clientPdo->exec("DELETE FROM settings");
    
    // Copy all settings from working database
    $stmt = $workingPdo->query("SELECT * FROM settings");
    $workingAllSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($workingAllSettings) . " settings in working database\n";
    
    $insertSql = "INSERT INTO settings (`group`, `key`, value, type, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
    $insertStmt = $clientPdo->prepare($insertSql);
    
    $copied = 0;
    foreach ($workingAllSettings as $setting) {
        try {
            $insertStmt->execute([
                $setting['group'],
                $setting['key'],
                $setting['value'],
                $setting['type']
            ]);
            $copied++;
        } catch (Exception $e) {
            echo "  Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Successfully copied $copied settings\n";
    
    // Verify
    $stmt = $clientPdo->query("SELECT COUNT(*) FROM settings");
    $finalCount = $stmt->fetchColumn();
    echo "Final client settings count: $finalCount\n";
    
    // Show navbar settings specifically
    echo "\n5. CLIENT 10 NAVBAR SETTINGS:\n";
    $stmt = $clientPdo->query("SELECT * FROM settings WHERE `group` = 'navbar'");
    $clientNavbarSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($clientNavbarSettings as $setting) {
        echo "  {$setting['key']}: {$setting['value']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== STRUCTURE CHECK COMPLETE ===\n";
