<?php

echo "=== TESTING FIXED API ENDPOINT ===\n\n";

try {
    echo "1. TESTING API WITHOUT WEBSITE PARAMETER:\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/smartprep/api/ui-settings');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Code: $httpCode\n";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            $brandName = $data['data']['navbar']['brand_name'] ?? 'NOT_FOUND';
            echo "  ✓ Main DB Brand Name: $brandName\n";
        } else {
            echo "  ❌ Invalid response format\n";
        }
    } else {
        echo "  ❌ Request failed\n";
    }
    
    echo "\n2. TESTING API WITH WEBSITE=10 PARAMETER:\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/smartprep/api/ui-settings?website=10');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Code: $httpCode\n";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            $brandName = $data['data']['navbar']['brand_name'] ?? 'NOT_FOUND';
            echo "  ✓ Tenant DB Brand Name: $brandName\n";
            
            // Check if it's different from main DB
            if ($brandName !== 'client') {
                echo "  ✓ Successfully reading from tenant database!\n";
            } else {
                echo "  ⚠ Still showing main DB value\n";
            }
            
            // Show some navbar settings to verify
            echo "  Navbar settings count: " . count($data['data']['navbar'] ?? []) . "\n";
            
            if (isset($data['data']['navbar'])) {
                echo "  Sample navbar settings:\n";
                $count = 0;
                foreach ($data['data']['navbar'] as $key => $value) {
                    if ($count < 5) {
                        $displayValue = strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value;
                        echo "    $key: $displayValue\n";
                        $count++;
                    }
                }
            }
        } else {
            echo "  ❌ Invalid response format\n";
        }
    } else {
        echo "  ❌ Request failed\n";
        echo "  Response: " . substr($response, 0, 200) . "\n";
    }
    
    echo "\n3. TESTING DIRECT DATABASE ACCESS:\n";
    
    $clientPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep_client-smartprep-local', 'root', '');
    $stmt = $clientPdo->prepare("SELECT value FROM settings WHERE `group` = 'navbar' AND `key` = 'brand_name'");
    $stmt->execute();
    $directBrandName = $stmt->fetchColumn();
    
    echo "  Direct DB query result: $directBrandName\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "If the API is working correctly, you should see different brand names\n";
echo "between main DB and tenant DB, and the preview should now update!\n";
