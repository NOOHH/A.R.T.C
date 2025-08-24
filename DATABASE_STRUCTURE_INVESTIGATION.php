<?php
/**
 * Database Structure Investigation
 */

echo "🔍 DATABASE STRUCTURE INVESTIGATION\n";
echo "===================================\n\n";

try {
    // Connect to main database
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to smartprep database\n\n";
    
    // Check what tables exist
    echo "📋 Available Tables:\n";
    echo "-------------------\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    // Check if we have any website-related tables
    $websiteTables = array_filter($tables, function($table) {
        return strpos($table, 'website') !== false || strpos($table, 'tenant') !== false;
    });
    
    if (!empty($websiteTables)) {
        echo "\n🏢 Website/Tenant Related Tables:\n";
        echo "--------------------------------\n";
        foreach ($websiteTables as $table) {
            echo "- $table\n";
            
            // Show structure
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $column) {
                echo "  └─ {$column['Field']} ({$column['Type']})\n";
            }
            echo "\n";
        }
    }
    
    // Check if settings table exists
    if (in_array('settings', $tables)) {
        echo "⚙️ Settings Table Structure:\n";
        echo "---------------------------\n";
        $stmt = $pdo->query("DESCRIBE settings");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Show sample settings
        echo "\n📄 Sample Settings (first 10):\n";
        echo "------------------------------\n";
        $stmt = $pdo->query("SELECT * FROM settings LIMIT 10");
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($settings as $setting) {
            echo "- {$setting['section']}.{$setting['setting_key']} = " . substr($setting['setting_value'], 0, 50) . "\n";
        }
    }
    
    // Look for any multi-tenant setup
    echo "\n🔍 Looking for Multi-Tenant Setup:\n";
    echo "----------------------------------\n";
    
    // Check for tenant databases
    $stmt = $pdo->query("SHOW DATABASES LIKE 'smartprep_%'");
    $tenantDbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($tenantDbs)) {
        echo "🏢 Found Tenant Databases:\n";
        foreach ($tenantDbs as $db) {
            echo "- $db\n";
        }
        
        // Check first tenant database
        $firstTenantDb = $tenantDbs[0];
        echo "\n📋 Tables in $firstTenantDb:\n";
        echo "----------------------------\n";
        
        $tenantPdo = new PDO("mysql:host=localhost;dbname=$firstTenantDb", 'root', '');
        $stmt = $tenantPdo->query("SHOW TABLES");
        $tenantTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tenantTables as $table) {
            echo "- $table\n";
        }
        
        // Check settings in tenant database
        if (in_array('settings', $tenantTables)) {
            echo "\n⚙️ Tenant Settings Sample:\n";
            echo "-------------------------\n";
            $stmt = $tenantPdo->query("SELECT * FROM settings WHERE section = 'auth' OR section = 'navbar' OR section = 'general' LIMIT 10");
            $tenantSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($tenantSettings as $setting) {
                echo "- {$setting['section']}.{$setting['setting_key']} = " . substr($setting['setting_value'], 0, 50) . "\n";
            }
        }
    } else {
        echo "❌ No tenant databases found\n";
    }
    
    // Check for application-specific tables that might store tenant info
    $possibleTenantTables = ['tenants', 'clients', 'organizations', 'companies', 'accounts'];
    foreach ($possibleTenantTables as $tableName) {
        if (in_array($tableName, $tables)) {
            echo "\n🏢 Found $tableName table:\n";
            echo str_repeat("-", strlen($tableName) + 13) . "\n";
            
            $stmt = $pdo->query("DESCRIBE $tableName");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $column) {
                echo "- {$column['Field']} ({$column['Type']})\n";
            }
            
            // Show sample data
            $stmt = $pdo->query("SELECT * FROM $tableName LIMIT 5");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($data)) {
                echo "\nSample data:\n";
                foreach ($data as $row) {
                    echo "- ID: {$row[array_keys($row)[0]]}\n";
                    foreach ($row as $key => $value) {
                        if ($key !== array_keys($row)[0]) {
                            echo "  $key: " . substr($value, 0, 30) . "\n";
                        }
                    }
                    echo "\n";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";
?>
