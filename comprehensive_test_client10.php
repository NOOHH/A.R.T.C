<?php

echo "=== COMPREHENSIVE CLIENT 10 FUNCTIONALITY TEST ===\n\n";

try {
    echo "1. TESTING DATABASE CONNECTION:\n";
    
    $clientPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep_client-smartprep-local', 'root', '');
    echo "  ✓ Connected to client database\n";
    
    echo "\n2. TESTING SETTINGS TABLE:\n";
    
    $stmt = $clientPdo->query("SELECT COUNT(*) FROM settings");
    $settingsCount = $stmt->fetchColumn();
    echo "  ✓ Settings table exists with $settingsCount records\n";
    
    echo "\n3. TESTING NAVBAR SETTINGS:\n";
    
    $stmt = $clientPdo->query("SELECT COUNT(*) FROM settings WHERE `group` = 'navbar'");
    $navbarCount = $stmt->fetchColumn();
    echo "  ✓ Found $navbarCount navbar settings\n";
    
    // Show current navbar settings
    $stmt = $clientPdo->query("SELECT `key`, value FROM settings WHERE `group` = 'navbar' ORDER BY `key`");
    $navbarSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "  Current navbar settings:\n";
    foreach ($navbarSettings as $setting) {
        $value = strlen($setting['value']) > 50 ? substr($setting['value'], 0, 50) . '...' : $setting['value'];
        echo "    {$setting['key']}: $value\n";
    }
    
    echo "\n4. TESTING UPDATE FUNCTIONALITY:\n";
    
    $testBrandName = 'FINAL_TEST_' . date('His');
    
    // Test the exact same query that the controller uses
    $updateSql = "INSERT INTO settings (`group`, `key`, value, type, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, NOW(), NOW()) 
                  ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()";
    
    $stmt = $clientPdo->prepare($updateSql);
    $result = $stmt->execute(['navbar', 'brand_name', $testBrandName, 'text']);
    
    if ($result) {
        echo "  ✓ Brand name update executed successfully\n";
        
        // Verify the update
        $stmt = $clientPdo->prepare("SELECT value FROM settings WHERE `group` = 'navbar' AND `key` = 'brand_name'");
        $stmt->execute();
        $savedBrand = $stmt->fetchColumn();
        
        if ($savedBrand === $testBrandName) {
            echo "  ✓ Brand name correctly saved as: $savedBrand\n";
        } else {
            echo "  ❌ Brand name mismatch: expected '$testBrandName', got '$savedBrand'\n";
        }
    } else {
        echo "  ❌ Brand name update failed\n";
    }
    
    echo "\n5. TESTING MULTIPLE FIELD UPDATES:\n";
    
    $multipleUpdates = [
        'header_bg' => '#' . dechex(rand(0, 16777215)),
        'header_text' => '#' . dechex(rand(0, 16777215)),
        'footer_bg' => '#' . dechex(rand(0, 16777215))
    ];
    
    foreach ($multipleUpdates as $key => $value) {
        $stmt = $clientPdo->prepare($updateSql);
        $result = $stmt->execute(['navbar', $key, $value, 'color']);
        
        if ($result) {
            echo "  ✓ Updated $key to $value\n";
        } else {
            echo "  ❌ Failed to update $key\n";
        }
    }
    
    echo "\n6. TESTING CURL REQUEST TO ACTUAL ENDPOINT:\n";
    
    // Test the actual Laravel endpoint
    $postData = [
        'brand_name' => 'CURL_TEST_' . date('His'),
        'header_bg' => '#00ff00',
        'header_text' => '#000000'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/smartprep/dashboard/settings/navbar/10');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "  HTTP Response Code: $httpCode\n";
    
    if ($curlError) {
        echo "  ❌ CURL Error: $curlError\n";
    } elseif ($httpCode == 200) {
        echo "  ✓ Request successful (200 OK)\n";
        echo "  Response: " . substr($response, 0, 200) . "...\n";
        
        // Verify the update was actually saved
        $stmt = $clientPdo->prepare("SELECT value FROM settings WHERE `group` = 'navbar' AND `key` = 'brand_name'");
        $stmt->execute();
        $finalBrand = $stmt->fetchColumn();
        
        if ($finalBrand === $postData['brand_name']) {
            echo "  ✓ Endpoint update verified: $finalBrand\n";
        } else {
            echo "  ⚠ Endpoint response was 200 but setting not updated\n";
        }
        
    } elseif ($httpCode == 500) {
        echo "  ❌ 500 Internal Server Error still occurring\n";
        echo "  Response: " . substr($response, 0, 500) . "\n";
    } else {
        echo "  ⚠ Unexpected response code: $httpCode\n";
        echo "  Response: " . substr($response, 0, 200) . "...\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
