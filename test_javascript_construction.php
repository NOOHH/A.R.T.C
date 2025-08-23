<?php
/**
 * Test JavaScript URL Construction - Check if our JS functions work correctly
 */

echo "🔧 JAVASCRIPT URL CONSTRUCTION TEST\n";
echo "====================================\n";

$tenant = 'test11';
$base_url = "http://127.0.0.1:8000/t/draft/{$tenant}";
$page_url = "{$base_url}/admin/courses/upload?website=15&preview=true";

echo "Fetching course content upload page to check JavaScript...\n";
echo "URL: {$page_url}\n\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($page_url, false, $context);

if ($response === false) {
    echo "❌ Failed to fetch page\n";
    exit;
}

// Extract JavaScript functions to test them
echo "📋 CHECKING JAVASCRIPT FUNCTIONS:\n";
echo "----------------------------------\n";

// Check for getTenantFromPath function
if (preg_match('/function getTenantFromPath\(\)\s*{([^}]+)}/s', $response, $matches)) {
    echo "✅ Found getTenantFromPath() function\n";
    echo "Function body: " . trim($matches[1]) . "\n\n";
} else {
    echo "❌ getTenantFromPath() function not found\n\n";
}

// Check for getApiUrl function
if (preg_match('/function getApiUrl\(endpoint\)\s*{([^}]+)}/s', $response, $matches)) {
    echo "✅ Found getApiUrl() function\n";
    echo "Function body: " . trim($matches[1]) . "\n\n";
} else {
    echo "❌ getApiUrl() function not found\n\n";
}

// Check for usage in populateModules
if (preg_match('/fetch\(getApiUrl\([\'"]modules\/by-program[\'"]/', $response)) {
    echo "✅ populateModules() uses getApiUrl()\n";
} else {
    echo "❌ populateModules() doesn't use getApiUrl()\n";
}

// Check for usage in populateCourses  
if (preg_match('/fetch\(getApiUrl\([\'"]modules\//', $response)) {
    echo "✅ populateCourses() uses getApiUrl()\n";
} else {
    echo "❌ populateCourses() doesn't use getApiUrl()\n";
}

// Simulate what the JavaScript would produce
echo "\n🔄 SIMULATING JAVASCRIPT URL CONSTRUCTION:\n";
echo "-------------------------------------------\n";

// Simulate the tenant extraction from current page URL
$current_path = "/t/draft/test11/admin/courses/upload";
if (preg_match('/\/t\/draft\/([^\/]+)\//', $current_path, $matches)) {
    $extracted_tenant = $matches[1];
    echo "✅ Tenant extracted from path: '{$extracted_tenant}'\n";
    
    // Simulate getApiUrl() construction
    $api_endpoint = 'modules/by-program';
    $constructed_url = "/t/draft/{$extracted_tenant}/admin/{$api_endpoint}";
    echo "✅ Constructed API URL: '{$constructed_url}'\n";
    
    $full_api_url = "http://127.0.0.1:8000{$constructed_url}?program_id=1";
    echo "✅ Full API URL would be: '{$full_api_url}'\n";
    
} else {
    echo "❌ Failed to extract tenant from path\n";
}

echo "\n🏁 JavaScript test completed!\n";
?>
