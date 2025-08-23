<?php
echo "=== CHECKING DATABASE STRUCTURE ===\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep;charset=utf8mb4', 'root', '');
    
    echo "1. Checking clients table structure...\n";
    $clientsColumns = $pdo->query("DESCRIBE clients")->fetchAll();
    
    foreach ($clientsColumns as $col) {
        echo "   - {$col['Field']} ({$col['Type']})\n";
    }
    
    echo "\n2. Checking clients data...\n";
    $clients = $pdo->query("SELECT * FROM clients WHERE id IN (15, 16)")->fetchAll();
    
    foreach ($clients as $client) {
        echo "   Client {$client['id']}: {$client['name']} (slug: {$client['slug']}, status: {$client['status']})\n";
    }
    
    echo "\n3. Checking tenants table structure...\n";
    try {
        $tenantsColumns = $pdo->query("DESCRIBE tenants")->fetchAll();
        
        foreach ($tenantsColumns as $col) {
            echo "   - {$col['Field']} ({$col['Type']})\n";
        }
        
        echo "\n4. Checking tenants data...\n";
        $tenants = $pdo->query("SELECT * FROM tenants")->fetchAll();
        
        if (empty($tenants)) {
            echo "   ❌ No tenants found!\n";
        } else {
            foreach ($tenants as $tenant) {
                echo "   Tenant {$tenant['id']}: {$tenant['name']} (slug: {$tenant['slug']}, domain: {$tenant['domain']}, db: {$tenant['database_name']})\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Tenants table error: " . $e->getMessage() . "\n";
    }
    
    echo "\n5. Checking what tenant databases exist...\n";
    $databases = $pdo->query("SHOW DATABASES LIKE 'smartprep_%'")->fetchAll();
    
    foreach ($databases as $db) {
        echo "   - {$db['Database (smartprep_%)']} \n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== STRUCTURE CHECK COMPLETE ===\n";
?>
