<?php
echo "=== CHECKING TENANT SETUP ===\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep;charset=utf8mb4', 'root', '');
    
    echo "1. Checking clients table...\n";
    $clients = $pdo->query("SELECT id, name, slug, database_name, status FROM clients WHERE id IN (15, 16)")->fetchAll();
    
    foreach ($clients as $client) {
        echo "   Client {$client['id']}: {$client['name']} (slug: {$client['slug']}, db: {$client['database_name']}, status: {$client['status']})\n";
    }
    
    echo "\n2. Checking tenants table...\n";
    $tenants = $pdo->query("SELECT id, name, slug, domain, database_name FROM tenants")->fetchAll();
    
    if (empty($tenants)) {
        echo "   ❌ No tenants found in tenants table!\n";
    } else {
        foreach ($tenants as $tenant) {
            echo "   Tenant {$tenant['id']}: {$tenant['name']} (slug: {$tenant['slug']}, domain: {$tenant['domain']}, db: {$tenant['database_name']})\n";
        }
    }
    
    echo "\n3. Checking if tenant records exist for our test clients...\n";
    foreach ($clients as $client) {
        $tenantMatch = $pdo->query("SELECT * FROM tenants WHERE slug = '{$client['slug']}'")->fetch();
        
        if ($tenantMatch) {
            echo "   ✅ Client {$client['id']} ({$client['slug']}) has matching tenant record\n";
        } else {
            echo "   ❌ Client {$client['id']} ({$client['slug']}) has NO matching tenant record\n";
            echo "      This explains why the controller can't switch to tenant database!\n";
        }
    }
    
    echo "\n4. Creating missing tenant records...\n";
    foreach ($clients as $client) {
        $tenantMatch = $pdo->query("SELECT * FROM tenants WHERE slug = '{$client['slug']}'")->fetch();
        
        if (!$tenantMatch) {
            echo "   Creating tenant record for client {$client['id']} ({$client['slug']})...\n";
            
            $stmt = $pdo->prepare("
                INSERT INTO tenants (name, slug, domain, database_name, created_at, updated_at)
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ");
            
            $domain = $client['slug'] . '.smartprep.local';
            $databaseName = $client['database_name'] ?: ('smartprep_' . $client['slug']);
            
            $stmt->execute([
                $client['name'],
                $client['slug'],
                $domain,
                $databaseName
            ]);
            
            echo "     ✅ Tenant record created: {$client['name']} -> {$databaseName}\n";
        }
    }
    
    echo "\n5. Verifying tenant records now exist...\n";
    $tenantsAfter = $pdo->query("SELECT id, name, slug, domain, database_name FROM tenants")->fetchAll();
    
    foreach ($tenantsAfter as $tenant) {
        echo "   Tenant {$tenant['id']}: {$tenant['name']} (slug: {$tenant['slug']}, domain: {$tenant['domain']}, db: {$tenant['database_name']})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== SETUP COMPLETE ===\n";
?>
