<?php
// Create client record for smartprep tenant and test customization

echo "🔧 FIXING SMARTPREP TENANT CUSTOMIZATION\n";
echo "========================================\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if smartprep client already exists
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE slug = 'smartprep'");
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        echo "Creating smartprep client record...\n";
        $stmt = $pdo->prepare("INSERT INTO clients (name, slug, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute(['SmartPrep', 'smartprep']);
        $clientId = $pdo->lastInsertId();
        echo "✅ SmartPrep client created with ID: $clientId\n\n";
    } else {
        $clientId = $client['id'];
        echo "✅ SmartPrep client already exists with ID: $clientId\n\n";
    }
    
    // Test URLs with correct website parameter
    $testUrls = [
        "http://127.0.0.1:8000/t/draft/smartprep/admin-dashboard?website=$clientId",
        "http://127.0.0.1:8000/t/draft/smartprep/admin/students?website=$clientId",
        "http://127.0.0.1:8000/t/draft/smartprep/admin/programs?website=$clientId"
    ];
    
    echo "🧪 TESTING SMARTPREP WITH WEBSITE PARAMETER\n";
    echo "===========================================\n\n";
    
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
                
                if (str_contains($title, 'SmartPrep') || str_contains($title, 'TEST11')) {
                    echo "✅ Tenant branding in title\n";
                } else {
                    echo "❌ No tenant branding in title\n";
                }
            }
            
            // Check for navbar customization
            if (preg_match('/<.*class="navbar-brand"[^>]*>([^<]+)/i', $body, $matches)) {
                $brand = trim($matches[1]);
                echo "Navbar Brand: $brand\n";
                
                if (str_contains($brand, 'SmartPrep') || str_contains($brand, 'TEST11')) {
                    echo "✅ Tenant branding in navbar\n";
                } else {
                    echo "❌ No tenant branding in navbar\n";
                }
            } else {
                echo "❌ Navbar brand not found\n";
            }
            
            // Check for settings variables
            if (str_contains($body, 'settings') && str_contains($body, 'navbar')) {
                echo "✅ Settings variables detected\n";
            } else {
                echo "❌ No settings variables\n";
            }
            
        } else {
            echo "❌ Failed with HTTP $httpCode\n";
        }
        
        curl_close($curl);
        echo "---\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
