<?php
echo "=== FINAL NAVBAR BRAND NAME TEST ===\n\n";

// Database connection
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'smartprep';
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ Connected to SmartPrep database\n\n";
    
    // Check current brand name in database
    $stmt = $pdo->prepare("SELECT * FROM ui_settings WHERE section = 'navbar' AND setting_key = 'brand_name'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "📊 Current database value:\n";
        echo "   Section: " . $result['section'] . "\n";
        echo "   Key: " . $result['setting_key'] . "\n";
        echo "   Value: " . $result['setting_value'] . "\n\n";
    } else {
        echo "❌ No brand_name setting found in database\n\n";
    }
    
    // Check what the navbar view would receive
    echo "=== TESTING VIEW DATA ===\n";
    
    // Simulate what UiSetting::getSection('navbar') returns
    $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM ui_settings WHERE section = 'navbar'");
    $stmt->execute();
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    echo "Navbar data that would be passed to view:\n";
    print_r($settings);
    
    // Test the specific brand name logic
    $brand_name = $settings['brand_name'] ?? 'Ascendo Review and Training Center';
    echo "\n🎯 Final brand name that should appear: '$brand_name'\n\n";
    
    echo "=== INSTRUCTIONS ===\n";
    echo "1. Hard refresh your browser (Ctrl+F5)\n";
    echo "2. Or open in incognito/private mode\n";
    echo "3. Check both the main navbar AND the footer\n";
    echo "4. If still not working, go to SmartPrep settings and update it through the form\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
