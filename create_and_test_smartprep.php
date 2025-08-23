<?php
// Create smartprep tenant and check admin customization system

echo "🔧 CREATING SMARTPREP TENANT AND CHECKING CUSTOMIZATION\n";
echo "======================================================\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if smartprep already exists
    $stmt = $pdo->prepare("SELECT * FROM tenants WHERE slug = 'smartprep'");
    $stmt->execute();
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tenant) {
        echo "Creating smartprep tenant...\n";
        $stmt = $pdo->prepare("INSERT INTO tenants (name, slug, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute(['SmartPrep', 'smartprep', 'active']);
        echo "✅ SmartPrep tenant created\n\n";
    } else {
        echo "✅ SmartPrep tenant already exists\n\n";
    }
    
    // Check what tables exist for customization
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Available tables:\n";
    $customizationTables = [];
    foreach ($tables as $table) {
        if (str_contains($table, 'admin') || str_contains($table, 'customiz') || str_contains($table, 'setting') || str_contains($table, 'tenant')) {
            $customizationTables[] = $table;
            echo "  - $table\n";
        }
    }
    
    echo "\n🔍 CHECKING ADMIN CUSTOMIZATION LOGIC\n";
    echo "=====================================\n";
    
    // Check if admin_customization table exists
    if (in_array('admin_customization', $customizationTables)) {
        echo "✅ admin_customization table exists\n";
        $stmt = $pdo->query("SELECT * FROM admin_customization LIMIT 5");
        $customizations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($customizations) {
            echo "Found customizations:\n";
            foreach ($customizations as $custom) {
                echo "  ID: {$custom['id']}, Website: {$custom['website_id']}\n";
            }
        } else {
            echo "❌ No customizations found\n";
        }
    }
    
    // Check admin_settings
    if (in_array('admin_settings', $customizationTables)) {
        echo "\n✅ admin_settings table exists\n";
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM admin_settings WHERE setting_key LIKE '%website%' OR setting_key LIKE '%name%' LIMIT 10");
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($settings) {
            echo "Admin settings:\n";
            foreach ($settings as $setting) {
                echo "  {$setting['setting_key']}: {$setting['setting_value']}\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🧪 TESTING SMARTPREP TENANT NOW\n";
echo "================================\n";

$testUrl = 'http://127.0.0.1:8000/t/draft/smartprep/admin-dashboard';
echo "Testing: $testUrl\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $testUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_HEADER => true,
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

echo "HTTP Status: $httpCode\n";

if ($httpCode == 200) {
    $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);
    
    if (preg_match('/<title>(.+?)<\/title>/i', $body, $matches)) {
        echo "Title: " . trim($matches[1]) . "\n";
    }
    
    if (str_contains($body, 'smartprep') || str_contains($body, 'SmartPrep')) {
        echo "✅ SmartPrep branding detected\n";
    } else {
        echo "❌ No SmartPrep branding detected\n";
    }
}

curl_close($curl);
?>
