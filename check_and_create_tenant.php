<?php
// Check what tenants exist in database

echo "🔍 CHECKING EXISTING TENANTS\n";
echo "============================\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all tenants
    $stmt = $pdo->query("SELECT * FROM tenants ORDER BY id");
    $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($tenants) {
        echo "✅ Found " . count($tenants) . " tenants:\n\n";
        foreach ($tenants as $tenant) {
            echo "Tenant ID: {$tenant['id']}\n";
            echo "Name: {$tenant['name']}\n";
            echo "Slug: {$tenant['slug']}\n";
            echo "Status: {$tenant['status']}\n";
            echo "---\n";
        }
    } else {
        echo "❌ No tenants found in database\n";
    }
    
    // Check website_settings for existing tenants
    echo "\n🔍 CHECKING WEBSITE SETTINGS\n";
    $stmt = $pdo->query("SELECT DISTINCT website_id FROM website_settings ORDER BY website_id");
    $websiteIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if ($websiteIds) {
        echo "✅ Found website settings for IDs: " . implode(', ', $websiteIds) . "\n\n";
        
        // Check settings for first website ID
        $firstId = $websiteIds[0];
        $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM website_settings WHERE website_id = ? AND (setting_key LIKE '%name%' OR setting_key LIKE '%title%' OR setting_key LIKE '%brand%') LIMIT 10");
        $stmt->execute([$firstId]);
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Website ID $firstId settings:\n";
        foreach ($settings as $setting) {
            echo "  {$setting['setting_key']}: {$setting['setting_value']}\n";
        }
    }
    
    echo "\n🔍 CREATING SMARTPREP TENANT\n";
    echo "Creating missing smartprep tenant...\n";
    
    $stmt = $pdo->prepare("INSERT INTO tenants (name, slug, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->execute(['SmartPrep', 'smartprep', 'active']);
    
    echo "✅ SmartPrep tenant created successfully\n";
    
    // Get the created tenant
    $tenantId = $pdo->lastInsertId();
    echo "New tenant ID: $tenantId\n";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>
