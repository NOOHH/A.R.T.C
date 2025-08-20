<?php
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING NAVBAR COMPOSER IN WEB CONTEXT ===\n\n";

// Make a simple HTTP request to the tenant URL and capture the response
$url = 'http://127.0.0.1:8000/t/client?website=10&preview=true';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: PHP Test',
            'Accept: text/html'
        ]
    ]
]);

echo "Making request to: {$url}\n";
$response = file_get_contents($url, false, $context);

// Check what brand name appears in the response
if (preg_match('/class="navbar-brand[^>]*>.*?([^<]+)/s', $response, $matches)) {
    echo "Found navbar brand: " . trim($matches[1]) . "\n";
} else {
    echo "No navbar brand found\n";
}

// Look for any debug output that might be in the HTML
if (preg_match('/DEBUG_TEST_232023/', $response)) {
    echo "✅ Found DEBUG_TEST_232023 in response\n";
} else {
    echo "❌ DEBUG_TEST_232023 not found in response\n";
}

// Check for composer variables in the HTML source
if (preg_match('/\$navbar.*?brand_name.*?([^"\']+)/i', $response, $matches)) {
    echo "Found navbar variable: " . $matches[1] . "\n";
}

echo "\nResponse length: " . strlen($response) . " bytes\n";
