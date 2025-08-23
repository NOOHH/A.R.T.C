<?php
echo "=== CHECKING CLIENT DB NAMES ===\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep;charset=utf8mb4', 'root', '');
    
    echo "1. Checking client db_name values...\n";
    $clients = $pdo->query("SELECT id, name, slug, db_name FROM clients WHERE id IN (15, 16)")->fetchAll();
    
    foreach ($clients as $client) {
        echo "   Client {$client['id']}: {$client['name']} (slug: {$client['slug']}, db_name: '{$client['db_name']}')\n";
    }
    
    echo "\n2. Checking corresponding tenant records...\n";
    foreach ($clients as $client) {
        $tenant = $pdo->query("SELECT * FROM tenants WHERE slug = '{$client['slug']}'")->fetch();
        
        if ($tenant) {
            echo "   Client {$client['id']} ({$client['slug']}) -> Tenant {$tenant['id']} (db: {$tenant['database_name']})\n";
            
            if ($client['db_name'] === $tenant['database_name']) {
                echo "     ✅ Client db_name matches tenant database_name\n";
            } else {
                echo "     ⚠️  Client db_name ('{$client['db_name']}') != tenant database_name ('{$tenant['database_name']}')\n";
                
                // Update client db_name to match tenant
                $stmt = $pdo->prepare("UPDATE clients SET db_name = ? WHERE id = ?");
                $stmt->execute([$tenant['database_name'], $client['id']]);
                echo "     ✅ Updated client db_name to match tenant\n";
            }
        } else {
            echo "   ❌ No tenant found for client {$client['id']} ({$client['slug']})\n";
        }
    }
    
    echo "\n3. Final verification...\n";
    $clientsAfter = $pdo->query("SELECT id, name, slug, db_name FROM clients WHERE id IN (15, 16)")->fetchAll();
    
    foreach ($clientsAfter as $client) {
        echo "   Client {$client['id']}: {$client['name']} (slug: {$client['slug']}, db_name: '{$client['db_name']}')\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CLIENT DB SETUP COMPLETE ===\n";
?>
