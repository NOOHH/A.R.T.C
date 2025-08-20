<?php

echo "=== DEBUGGING DATABASE ISSUE ===\n\n";

try {
    $mainPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep', 'root', '');
    
    echo "1. CHECKING CLIENTS:\n";
    $stmt = $mainPdo->query("SELECT id, name, slug, domain, db_name, user_id FROM clients ORDER BY id");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($clients as $client) {
        echo "  Client {$client['id']}: {$client['name']} (slug: {$client['slug']}, domain: {$client['domain']})\n";
        if ($client['id'] == 10) {
            echo "    *** THIS IS CLIENT 10 FROM THE ERROR ***\n";
        }
    }
    
    echo "\n2. CHECKING TENANTS:\n";
    $stmt = $mainPdo->query("SELECT id, name, slug, domain, database FROM tenants ORDER BY id");
    $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tenants as $tenant) {
        echo "  Tenant {$tenant['id']}: {$tenant['name']} (slug: {$tenant['slug']}, db: {$tenant['database']})\n";
    }
    
    echo "\n3. CHECKING EXISTING DATABASES:\n";
    $stmt = $mainPdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($databases as $dbName) {
        if (strpos($dbName, 'smartprep_') === 0) {
            echo "  - $dbName\n";
        }
    }
    
    echo "\n4. CHECKING CLIENT 10 SPECIFICALLY:\n";
    $stmt = $mainPdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([10]);
    $client10 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client10) {
        echo "  Found client 10:\n";
        echo "    Name: {$client10['name']}\n";
        echo "    Slug: {$client10['slug']}\n";
        echo "    Domain: {$client10['domain']}\n";
        echo "    DB Name: " . ($client10['db_name'] ?? 'NULL') . "\n";
        
        // Check corresponding tenant
        $stmt = $mainPdo->prepare("SELECT * FROM tenants WHERE slug = ?");
        $stmt->execute([$client10['slug']]);
        $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tenant) {
            echo "  Corresponding tenant:\n";
            echo "    Database: {$tenant['database']}\n";
            
            // Check if database exists
            $dbExists = in_array($tenant['database'], $databases);
            echo "    Database exists: " . ($dbExists ? 'YES' : 'NO') . "\n";
            
            if ($dbExists) {
                try {
                    $tenantPdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname={$tenant['database']}", 'root', '');
                    
                    // Check for settings table
                    $stmt = $tenantPdo->query("SHOW TABLES LIKE 'settings'");
                    $hasSettings = $stmt->rowCount() > 0;
                    echo "    Settings table exists: " . ($hasSettings ? 'YES' : 'NO') . "\n";
                    
                    if ($hasSettings) {
                        $stmt = $tenantPdo->query("SELECT COUNT(*) FROM settings");
                        $count = $stmt->fetchColumn();
                        echo "    Settings count: $count\n";
                    } else {
                        echo "    ❌ MISSING SETTINGS TABLE - THIS IS THE PROBLEM!\n";
                    }
                } catch (Exception $e) {
                    echo "    Error connecting to tenant database: " . $e->getMessage() . "\n";
                }
            } else {
                echo "    ❌ TENANT DATABASE DOESN'T EXIST - THIS IS THE PROBLEM!\n";
            }
        } else {
            echo "  ❌ NO CORRESPONDING TENANT FOUND\n";
        }
    } else {
        echo "  ❌ CLIENT 10 NOT FOUND\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
