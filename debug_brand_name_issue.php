<?php

echo "=== INVESTIGATING BRAND NAME UPDATE ISSUE ===\n\n";

try {
    $clientPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep_client-smartprep-local', 'root', '');
    
    echo "1. CHECKING CURRENT DATABASE VALUES:\n";
    
    // Check current brand name in database
    $stmt = $clientPdo->prepare("SELECT value, updated_at FROM settings WHERE `group` = 'navbar' AND `key` = 'brand_name'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "  Current brand_name in DB: '{$result['value']}'\n";
        echo "  Last updated: {$result['updated_at']}\n";
    } else {
        echo "  ❌ No brand_name setting found in database\n";
    }
    
    echo "\n2. CHECKING ALL RECENT NAVBAR UPDATES:\n";
    
    $stmt = $clientPdo->query("
        SELECT `key`, value, updated_at 
        FROM settings 
        WHERE `group` = 'navbar' 
        ORDER BY updated_at DESC 
        LIMIT 10
    ");
    $recentUpdates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($recentUpdates as $update) {
        echo "  {$update['key']}: '{$update['value']}' (updated: {$update['updated_at']})\n";
    }
    
    echo "\n3. TESTING MANUAL BRAND NAME UPDATE:\n";
    
    $testBrandName = 'DEBUG_TEST_' . date('His');
    
    $updateSql = "UPDATE settings SET value = ?, updated_at = NOW() WHERE `group` = 'navbar' AND `key` = 'brand_name'";
    $stmt = $clientPdo->prepare($updateSql);
    $result = $stmt->execute([$testBrandName]);
    
    if ($result) {
        echo "  ✓ Manual update successful\n";
        
        // Verify the update
        $stmt = $clientPdo->prepare("SELECT value FROM settings WHERE `group` = 'navbar' AND `key` = 'brand_name'");
        $stmt->execute();
        $newValue = $stmt->fetchColumn();
        
        if ($newValue === $testBrandName) {
            echo "  ✓ Database correctly shows: '$newValue'\n";
        } else {
            echo "  ❌ Database mismatch: expected '$testBrandName', got '$newValue'\n";
        }
    } else {
        echo "  ❌ Manual update failed\n";
    }
    
    echo "\n4. CHECKING IF PREVIEW IS READING FROM CORRECT DATABASE:\n";
    
    // Let's see what the preview might be reading
    echo "  Testing different possible sources:\n";
    
    // Check main database ui_settings
    $mainPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep', 'root', '');
    $stmt = $mainPdo->query("SELECT setting_value FROM ui_settings WHERE section = 'navbar' AND setting_key = 'brand_name'");
    $mainBrandName = $stmt->fetchColumn();
    
    if ($mainBrandName) {
        echo "    Main DB (ui_settings): '$mainBrandName'\n";
    } else {
        echo "    Main DB (ui_settings): No brand_name found\n";
    }
    
    // Check if there are any cached values or other tables
    $stmt = $clientPdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "    Available tables in tenant DB: " . implode(', ', $tables) . "\n";
    
    echo "\n5. CHECKING PREVIEW ENDPOINT RESPONSE:\n";
    
    // Test what the preview endpoint returns
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=10');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html',
        'User-Agent: Mozilla/5.0'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  Preview page HTTP code: $httpCode\n";
    
    if ($httpCode == 200 && $response) {
        // Look for brand name in the HTML
        if (preg_match('/brand.*?name.*?["\']([^"\']+)["\']/', $response, $matches)) {
            echo "  Brand name found in HTML: '{$matches[1]}'\n";
        } else {
            echo "  Could not find brand name pattern in HTML\n";
        }
        
        // Look for JavaScript variables
        if (preg_match('/brand_name["\']?\s*[:=]\s*["\']([^"\']+)["\']/', $response, $matches)) {
            echo "  Brand name in JS variables: '{$matches[1]}'\n";
        } else {
            echo "  Could not find brand name in JS variables\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== INVESTIGATION COMPLETE ===\n";
