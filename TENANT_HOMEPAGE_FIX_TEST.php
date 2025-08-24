<?php
// Comprehensive tenant homepage test after PreviewController fix

$tenant_url = "http://localhost:8000/t/draft/artc";

echo "==== TESTING TENANT HOMEPAGE AFTER PREVIEWCONTROLLER FIX ====\n\n";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tenant_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Test Script');
curl_setopt($ch, CURLOPT_VERBOSE, true);

$verbose_output = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose_output);

echo "Testing URL: $tenant_url\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

// Get verbose output
rewind($verbose_output);
$verbose_info = stream_get_contents($verbose_output);
fclose($verbose_output);

curl_close($ch);

echo "\n--- Response Results ---\n";
echo "HTTP Status Code: $httpCode\n";
echo "cURL Error: " . ($error ?: 'None') . "\n";
echo "Response Length: " . strlen($response) . " characters\n";

if ($httpCode == 200) {
    echo "\n✅ SUCCESS: Tenant homepage loaded successfully!\n";
    
    // Check for specific content
    $checks = [
        'ENROLL NOW button' => strpos($response, 'ENROLL NOW') !== false,
        'Hero section' => strpos($response, 'hero') !== false,
        'Programs section' => strpos($response, 'programs') !== false,
        'Tenant-aware enrollment URL' => strpos($response, '/t/draft/smartprep/enrollment') !== false,
        'No PHP errors' => strpos($response, 'Undefined variable') === false
    ];
    
    echo "\n--- Content Verification ---\n";
    foreach ($checks as $check => $passed) {
        $status = $passed ? '✅' : '❌';
        echo "$status $check\n";
    }
    
    // Extract any errors from response
    if (preg_match_all('/error|undefined|fatal/i', $response, $matches)) {
        echo "\n⚠️  Potential issues found:\n";
        foreach (array_unique($matches[0]) as $issue) {
            echo "- $issue\n";
        }
    }
    
} else {
    echo "\n❌ FAILED: HTTP $httpCode\n";
    
    if (strpos($response, 'Undefined variable') !== false) {
        echo "\n--- PHP Error Details ---\n";
        if (preg_match('/Undefined variable.*/', $response, $matches)) {
            echo "Error: " . $matches[0] . "\n";
        }
    }
    
    // Show first 500 characters of response for debugging
    echo "\n--- Response Preview ---\n";
    echo substr($response, 0, 500) . "\n";
}

echo "\n--- Verbose cURL Info ---\n";
echo $verbose_info;

echo "\n==== TEST COMPLETE ====\n";
?>
