<?php

echo "=== CHECKING TENANTS TABLE STRUCTURE ===\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=smartprep', 'root', '');
    
    echo "1. CHECKING TENANTS TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE tenants");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "  {$column['Field']}: {$column['Type']}\n";
    }
    
    echo "\n2. CHECKING TENANTS DATA:\n";
    $stmt = $pdo->query("SELECT * FROM tenants");
    $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tenants as $tenant) {
        echo "  Tenant {$tenant['id']}: {$tenant['name']}\n";
        foreach ($tenant as $key => $value) {
            echo "    $key: $value\n";
        }
        echo "  ---\n";
    }
    
    echo "\n3. CHECKING CLIENT 10 AND ITS TENANT:\n";
    
    // Get client 10
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = 10");
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client) {
        echo "Client 10 details:\n";
        foreach ($client as $key => $value) {
            echo "  $key: $value\n";
        }
        
        // Find tenant with matching slug
        $stmt = $pdo->prepare("SELECT * FROM tenants WHERE slug = ?");
        $stmt->execute([$client['slug']]);
        $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tenant) {
            echo "\nMatching tenant:\n";
            foreach ($tenant as $key => $value) {
                echo "  $key: $value\n";
            }
            
            // Check if tenant database exists
            $dbName = isset($tenant['database']) ? $tenant['database'] : $tenant['database_name'];
            echo "\nChecking database: $dbName\n";
            
            try {
                $tenantPdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=$dbName", 'root', '');
                echo "✓ Database exists and is accessible\n";
                
                // Check for settings table
                $stmt = $tenantPdo->query("SHOW TABLES LIKE 'settings'");
                if ($stmt->rowCount() > 0) {
                    echo "✓ Settings table exists\n";
                    
                    $stmt = $tenantPdo->query("SELECT COUNT(*) FROM settings");
                    $count = $stmt->fetchColumn();
                    echo "✓ Settings count: $count\n";
                } else {
                    echo "❌ Settings table missing\n";
                    echo "Need to create settings table and migrate data\n";
                }
                
            } catch (PDOException $e) {
                echo "❌ Database connection failed: " . $e->getMessage() . "\n";
            }
            
        } else {
            echo "\n❌ No tenant found with slug: {$client['slug']}\n";
        }
    } else {
        echo "❌ Client 10 not found\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETE ===\n";
