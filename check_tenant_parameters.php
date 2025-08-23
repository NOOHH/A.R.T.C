<?php
// Check what parameters are being passed to tenant preview URLs

echo "🔍 CHECKING TENANT PREVIEW URL PARAMETERS\n";
echo "==========================================\n\n";

// Test URLs with and without website parameter
$testUrls = [
    'http://127.0.0.1:8000/t/draft/smartprep/admin-dashboard',
    'http://127.0.0.1:8000/t/draft/smartprep/admin-dashboard?website=15',
    'http://127.0.0.1:8000/t/draft/test1/admin-dashboard',
    'http://127.0.0.1:8000/t/draft/test1/admin-dashboard?website=15'
];

foreach ($testUrls as $url) {
    echo "🔍 Testing: $url\n";
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 10
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    echo "HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body = substr($response, $headerSize);
        
        // Check title
        if (preg_match('/<title>(.+?)<\/title>/i', $body, $matches)) {
            $title = trim($matches[1]);
            echo "Title: $title\n";
        }
        
        // Check for customization
        if (str_contains($body, 'TEST11') || str_contains($body, 'SmartPrep')) {
            echo "✅ Tenant branding detected\n";
        } else {
            echo "❌ No tenant branding\n";
        }
        
        // Check for navbar brand
        if (preg_match('/<.*class="navbar-brand"[^>]*>([^<]+)/i', $body, $matches)) {
            $brand = trim($matches[1]);
            echo "Navbar Brand: $brand\n";
        } else {
            echo "❌ Navbar brand not found\n";
        }
        
        // Check if website parameter is mentioned in the source
        if (str_contains($body, 'website=') || str_contains($body, 'websiteId')) {
            echo "✅ Website parameter logic detected\n";
        } else {
            echo "❌ No website parameter logic\n";
        }
    }
    
    curl_close($curl);
    echo "---\n\n";
}

echo "🔍 CHECKING CLIENT TABLE FOR WEBSITE IDs\n";
echo "========================================\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if clients table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'clients'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Clients table exists\n";
        
        $stmt = $pdo->query("SELECT id, name, slug FROM clients LIMIT 10");
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($clients) {
            echo "Found clients:\n";
            foreach ($clients as $client) {
                echo "  ID: {$client['id']}, Slug: {$client['slug']}, Name: {$client['name']}\n";
            }
        } else {
            echo "❌ No clients found\n";
        }
    } else {
        echo "❌ Clients table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>
