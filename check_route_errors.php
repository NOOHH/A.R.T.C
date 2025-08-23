<?php
// Check for specific route errors in the responses
$url = 'http://localhost:8000/t/draft/test1/admin/payments/pending?website=15&preview=true';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Checking Payment Pending response for route errors:\n";
echo "Response length: " . strlen($response) . " bytes\n\n";

// Search for any route errors
if (preg_match_all('/Route \[([^\]]+)\] not defined/', $response, $matches)) {
    echo "❌ Route errors found:\n";
    foreach ($matches[1] as $routeName) {
        echo "   • Route [{$routeName}] not defined\n";
    }
} else {
    echo "✅ No route errors found in response\n";
}

// Search for other Laravel errors
if (strpos($response, 'Illuminate\\View\\ViewException') !== false) {
    echo "⚠️  View exception found in response\n";
}

if (strpos($response, 'not defined') !== false) {
    echo "⚠️  'not defined' text found somewhere in response\n";
    
    // Extract context around "not defined"
    $pos = strpos($response, 'not defined');
    $start = max(0, $pos - 100);
    $length = 200;
    $context = substr($response, $start, $length);
    echo "Context: " . $context . "\n";
}

?>
