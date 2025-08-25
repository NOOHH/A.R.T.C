<?php
// Enrollment URL validation script

echo "🔍 ENROLLMENT URL VALIDATION TEST\n";
echo "=================================\n\n";

// Test different tenant contexts
$testContexts = [
    [
        "name" => "Regular non-tenant",
        "base_url" => "http://127.0.0.1:8000",
        "enrollment_path" => "/enrollment",
        "modular_path" => "/enrollment/modular"
    ],
    [
        "name" => "ARTC Draft Tenant", 
        "base_url" => "http://127.0.0.1:8000",
        "enrollment_path" => "/t/draft/artc/enrollment",
        "modular_path" => "/t/draft/artc/enrollment/modular"
    ],
    [
        "name" => "ARTC Live Tenant",
        "base_url" => "http://127.0.0.1:8000", 
        "enrollment_path" => "/t/artc/enrollment",
        "modular_path" => "/t/artc/enrollment/modular"
    ]
];

foreach ($testContexts as $context) {
    echo "📋 Testing: {$context["name"]}\n";
    echo "   Base URL: {$context["base_url"]}\n";
    
    $enrollmentUrl = $context["base_url"] . $context["enrollment_path"];
    $modularUrl = $context["base_url"] . $context["modular_path"];
    
    // Test enrollment page
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $enrollmentUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $status = ($httpCode == 200 || $httpCode == 302) ? "✅" : "❌";
    echo "   $status Enrollment page: $enrollmentUrl (HTTP $httpCode)\n";
    
    // Test modular page
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $modularUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $status = ($httpCode == 200 || $httpCode == 302) ? "✅" : "❌";
    echo "   $status Modular page: $modularUrl (HTTP $httpCode)\n";
    
    echo "\n";
}

echo "=== VALIDATION COMPLETE ===\n";
?>