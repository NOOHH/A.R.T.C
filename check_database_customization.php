<?php
// Check database for tenant customization data

echo "🗄️  DATABASE TENANT CUSTOMIZATION CHECK\n";
echo "=====================================\n\n";

try {
    // Connect to database
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful\n\n";
    
    // Check if tenant table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'tenants'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tenants table exists\n";
        
        // Get smartprep tenant data
        $stmt = $pdo->prepare("SELECT * FROM tenants WHERE slug = 'smartprep' LIMIT 1");
        $stmt->execute();
        $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tenant) {
            echo "✅ SmartPrep tenant found:\n";
            echo "   ID: {$tenant['id']}\n";
            echo "   Name: {$tenant['name']}\n";
            echo "   Slug: {$tenant['slug']}\n";
            echo "   Status: {$tenant['status']}\n\n";
        } else {
            echo "❌ SmartPrep tenant NOT found in database\n\n";
        }
    } else {
        echo "❌ Tenants table does NOT exist\n\n";
    }
    
    // Check admin settings table
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_settings'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Admin_settings table exists\n";
        
        // Get customization settings
        $stmt = $pdo->query("SELECT * FROM admin_settings WHERE setting_key LIKE '%customiz%' OR setting_key LIKE '%brand%' OR setting_key LIKE '%logo%'");
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($settings) {
            echo "✅ Customization settings found:\n";
            foreach ($settings as $setting) {
                echo "   {$setting['setting_key']}: {$setting['setting_value']}\n";
            }
        } else {
            echo "❌ No customization settings found\n";
        }
    } else {
        echo "❌ Admin_settings table does NOT exist\n";
    }
    
    // Check tenant_settings or website_settings table
    $stmt = $pdo->query("SHOW TABLES LIKE 'website_settings'");
    if ($stmt->rowCount() > 0) {
        echo "\n✅ Website_settings table exists\n";
        
        $stmt = $pdo->query("SELECT * FROM website_settings WHERE website_id = 15 LIMIT 5");
        $webSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($webSettings) {
            echo "✅ Website settings for ID 15:\n";
            foreach ($webSettings as $setting) {
                echo "   {$setting['setting_key']}: {$setting['setting_value']}\n";
            }
        } else {
            echo "❌ No website settings for ID 15\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\nNext: Check session and request data\n";
?>
