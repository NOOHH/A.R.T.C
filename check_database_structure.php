<?php
// Check multiple databases and understand tenant structure

echo "🔍 CHECKING DATABASE STRUCTURE\n";
echo "===============================\n\n";

$databases = ['smartprep', 'artc'];

foreach ($databases as $dbName) {
    echo "🗄️  Checking database: $dbName\n";
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$dbName", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ Connected to $dbName\n";
        
        // Check for tenants table
        $stmt = $pdo->query("SHOW TABLES LIKE 'tenants'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->query("SELECT id, name, slug, status FROM tenants");
            $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Tenants in $dbName:\n";
            foreach ($tenants as $tenant) {
                echo "  - {$tenant['slug']} ({$tenant['name']})\n";
            }
        }
        
        // Check for website_settings table
        $stmt = $pdo->query("SHOW TABLES LIKE 'website_settings'");
        if ($stmt->rowCount() > 0) {
            echo "✅ website_settings table exists in $dbName\n";
            $stmt = $pdo->query("SELECT DISTINCT website_id FROM website_settings LIMIT 5");
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "Website IDs: " . implode(', ', $ids) . "\n";
        } else {
            echo "❌ No website_settings table in $dbName\n";
        }
        
        // Check for admin_customization table
        $stmt = $pdo->query("SHOW TABLES LIKE '%customiz%'");
        $customTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if ($customTables) {
            echo "Customization tables: " . implode(', ', $customTables) . "\n";
        }
        
        echo "\n";
        
    } catch (Exception $e) {
        echo "❌ Cannot connect to $dbName: " . $e->getMessage() . "\n\n";
    }
}

// Test with existing tenant
echo "🔍 TESTING WITH EXISTING TENANT 'test1'\n";
echo "=======================================\n\n";

$testUrl = 'http://127.0.0.1:8000/t/draft/test1/admin-dashboard';
echo "Testing URL: $testUrl\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $testUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_HEADER => true,
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($httpCode == 200) {
    $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);
    
    if (preg_match('/<title>(.+?)<\/title>/i', $body, $matches)) {
        echo "Title: " . trim($matches[1]) . "\n";
    }
    
    if (str_contains($body, 'test1') || str_contains($body, 'TEST1')) {
        echo "✅ test1 tenant branding detected\n";
    } else {
        echo "❌ No test1 tenant branding\n";
    }
} else {
    echo "❌ HTTP $httpCode\n";
}

curl_close($curl);
?>
