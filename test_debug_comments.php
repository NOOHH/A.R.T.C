<?php
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

$response = file_get_contents($url, false, $context);

// Look for debug comments
echo "=== SEARCHING FOR DEBUG COMMENTS ===\n";
if (preg_match_all('/<!--.*?DEBUG.*?-->/s', $response, $matches)) {
    echo "Found debug comments:\n";
    foreach ($matches[0] as $match) {
        echo "  " . $match . "\n";
    }
} else {
    echo "No debug comments found\n";
}

// Check if we're actually hitting the tenant URL
echo "\n=== URL ANALYSIS ===\n";
echo "Request URL: {$url}\n";
echo "Response length: " . strlen($response) . " bytes\n";

// Check for any mention of tenant in the response
if (preg_match_all('/tenant/i', $response, $matches)) {
    echo "Found 'tenant' mentions: " . count($matches[0]) . "\n";
} else {
    echo "No tenant mentions found\n";
}

// Check the URL in the response (might be redirected)
if (preg_match('/href="([^"]*)"/', $response, $matches)) {
    echo "Sample href found: " . $matches[1] . "\n";
}
