<?php
echo "=== CREATING TEST TO REPRODUCE SETTINGS CROSS-CONTAMINATION ===\n\n";

// Test by creating different brand names in each tenant database and checking if they're isolated

try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    
    echo "1. Setting up test data in tenant databases...\n";
    
    // Test data
    $testData = [
        'smartprep_test1' => 'BRAND_TEST1_ISOLATED',
        'smartprep_test2' => 'BRAND_TEST2_ISOLATED'
    ];
    
    foreach ($testData as $database => $brandName) {
        echo "   Setting up $database with brand: $brandName\n";
        
        try {
            $pdo->exec("USE `$database`");
            
            // Insert/update test brand name
            $stmt = $pdo->prepare("
                INSERT INTO ui_settings (section, setting_key, setting_value, setting_type, created_at, updated_at)
                VALUES ('navbar', 'brand_name', ?, 'text', NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = NOW()
            ");
            $stmt->execute([$brandName]);
            
            echo "     ✅ Brand name set successfully\n";
            
        } catch (Exception $e) {
            echo "     ❌ Error setting brand name: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n2. Verifying test data was set correctly...\n";
    
    foreach ($testData as $database => $expectedBrand) {
        $pdo->exec("USE `$database`");
        $actualBrand = $pdo->query("
            SELECT setting_value 
            FROM ui_settings 
            WHERE section = 'navbar' AND setting_key = 'brand_name'
        ")->fetchColumn();
        
        echo "   $database: Expected='$expectedBrand', Actual='$actualBrand'\n";
        
        if ($actualBrand === $expectedBrand) {
            echo "     ✅ Data correctly isolated\n";
        } else {
            echo "     ❌ Data mismatch!\n";
        }
    }
    
    echo "\n3. Now testing the web interface isolation...\n";
    
    // Test the customize-website endpoint for each website
    $testResults = [];
    
    foreach ([15 => 'test1', 16 => 'test2'] as $websiteId => $slug) {
        echo "   Testing website $websiteId ($slug)...\n";
        
        $url = "http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=$websiteId";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            // Try to extract the brand name from the response
            if (preg_match('/name="brand_name"[^>]*value="([^"]*)"/', $response, $matches)) {
                $webBrandName = $matches[1];
                $expectedBrand = $testData["smartprep_$slug"];
                
                echo "     HTTP 200 - Brand name in web: '$webBrandName'\n";
                echo "     Expected from database: '$expectedBrand'\n";
                
                if ($webBrandName === $expectedBrand) {
                    echo "     ✅ Web interface showing correct tenant-specific brand\n";
                    $testResults[$websiteId] = 'PASS';
                } else {
                    echo "     ❌ Web interface showing wrong brand (cross-contamination detected!)\n";
                    $testResults[$websiteId] = 'FAIL';
                }
            } else {
                echo "     ⚠️  Could not extract brand name from response\n";
                $testResults[$websiteId] = 'UNKNOWN';
            }
        } else {
            echo "     ❌ HTTP $httpCode - Error accessing web interface\n";
            $testResults[$websiteId] = 'ERROR';
        }
    }
    
    echo "\n=== TEST RESULTS ===\n";
    $allPassed = true;
    foreach ($testResults as $websiteId => $result) {
        echo "Website $websiteId: $result\n";
        if ($result !== 'PASS') {
            $allPassed = false;
        }
    }
    
    if ($allPassed) {
        echo "\n🎉 ALL TESTS PASSED - Settings are properly isolated!\n";
    } else {
        echo "\n💥 TESTS FAILED - Cross-contamination detected!\n";
        echo "Settings from one website are affecting another website.\n";
    }
    
} catch (Exception $e) {
    echo "Error during test: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
