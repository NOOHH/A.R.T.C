<?php
echo "=== CHECKING DATABASE STRUCTURE FOR CUSTOMIZATION ISOLATION ===\n\n";

try {
    // Direct database connection
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    
    echo "1. Checking available databases:\n";
    $databases = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    $smartprepDatabases = array_filter($databases, function($db) {
        return strpos($db, 'smartprep') !== false;
    });
    
    foreach ($smartprepDatabases as $db) {
        echo "   - $db\n";
    }
    
    echo "\n2. Checking main smartprep database clients:\n";
    $pdo->exec("USE smartprep");
    $clients = $pdo->query("SELECT id, name, slug FROM clients ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($clients as $client) {
        echo "   - ID: {$client['id']}, Name: {$client['name']}, Slug: {$client['slug']}\n";
    }
    
    echo "\n3. Checking tenants:\n";
    $tenants = $pdo->query("SELECT slug, name, database_name FROM tenants ORDER BY slug")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tenants as $tenant) {
        echo "   - Slug: {$tenant['slug']}, Name: {$tenant['name']}, DB: {$tenant['database_name']}\n";
    }
    
    echo "\n4. Checking settings in tenant databases:\n";
    foreach ($tenants as $tenant) {
        $dbName = $tenant['database_name'];
        if (in_array($dbName, $databases)) {
            echo "\n   Database: $dbName (tenant: {$tenant['slug']})\n";
            
            try {
                $pdo->exec("USE `$dbName`");
                
                // Check if settings table exists
                $tables = $pdo->query("SHOW TABLES LIKE 'settings'")->fetchAll();
                if (empty($tables)) {
                    echo "     - No settings table found\n";
                    continue;
                }
                
                // Check navbar settings
                $navbarSettings = $pdo->query("
                    SELECT `key`, `value` 
                    FROM settings 
                    WHERE section = 'navbar' 
                    AND `key` IN ('brand_name', 'navbar_brand_name')
                    ORDER BY `key`
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                if (empty($navbarSettings)) {
                    echo "     - No navbar brand settings found\n";
                } else {
                    foreach ($navbarSettings as $setting) {
                        echo "     - {$setting['key']}: {$setting['value']}\n";
                    }
                }
                
            } catch (Exception $e) {
                echo "     - Error accessing database: " . $e->getMessage() . "\n";
            }
        } else {
            echo "\n   Database: $dbName (tenant: {$tenant['slug']}) - NOT FOUND\n";
        }
    }
    
    echo "\n=== DATABASE CHECK COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
