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

// Find the navbar section
if (preg_match('/<nav[^>]*navbar[^>]*>.*?<\/nav>/s', $response, $matches)) {
    echo "=== NAVBAR SECTION ===\n";
    echo $matches[0] . "\n\n";
} else {
    echo "❌ Navbar section not found\n";
}

// Look for any occurrence of brand names
echo "=== SEARCHING FOR BRAND NAMES ===\n";
if (preg_match_all('/client|DEBUG_TEST_232023/i', $response, $matches)) {
    echo "Found brand name occurrences:\n";
    foreach (array_unique($matches[0]) as $match) {
        echo "  - {$match}\n";
    }
} else {
    echo "No brand name occurrences found\n";
}

// Look for any variables or debug info
echo "\n=== LOOKING FOR VARIABLES ===\n";
if (preg_match_all('/\$[a-zA-Z_][a-zA-Z0-9_]*/', $response, $matches)) {
    echo "Found variables:\n";
    foreach (array_unique($matches[0]) as $match) {
        echo "  - {$match}\n";
    }
}

echo "\n=== LOOKING FOR NAVBAR BRAND LINK ===\n";
if (preg_match('/<a[^>]*navbar-brand[^>]*>.*?<\/a>/s', $response, $matches)) {
    echo "Found navbar brand link:\n";
    echo $matches[0] . "\n";
}
