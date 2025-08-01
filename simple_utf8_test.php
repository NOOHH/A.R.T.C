<?php
// Simple test without Laravel bootstrapping
echo "=== Testing UTF-8 Fixes ===" . PHP_EOL;

// Test UTF-8 cleaning functions (the fixes we added)
$testContent = "Machine Design: Résistance and torsión calculations with special chars ©®™";
echo "Original: $testContent" . PHP_EOL;

// Apply the same UTF-8 fixes we added to the services
$cleaned = mb_convert_encoding($testContent, 'UTF-8', 'UTF-8');
$cleaned = iconv('UTF-8', 'UTF-8//IGNORE', $cleaned);
$cleaned = preg_replace('/[^\x20-\x7E\xA0-\xFF]/u', ' ', $cleaned);
$cleaned = preg_replace('/\s+/', ' ', trim($cleaned));

echo "Cleaned: $cleaned" . PHP_EOL;
echo "Length: " . strlen($cleaned) . " characters" . PHP_EOL;

// Test API payload structure (without responseFormat)
$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => "Generate 3 multiple choice questions from: $cleaned"]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.1,
        'topK' => 1,
        'topP' => 1,
        'maxOutputTokens' => 8192,
    ]
];

echo "\n=== API Payload Structure (no responseFormat) ===" . PHP_EOL;
echo "Payload keys: " . implode(', ', array_keys($payload)) . PHP_EOL;
echo "Generation config: " . implode(', ', array_keys($payload['generationConfig'])) . PHP_EOL;

// Test JSON encoding
$json = json_encode($payload);
if ($json === false) {
    echo "❌ JSON encoding failed: " . json_last_error_msg() . PHP_EOL;
} else {
    echo "✅ JSON encoding successful" . PHP_EOL;
    echo "JSON length: " . strlen($json) . " characters" . PHP_EOL;
}

echo "\n=== Content Limit Test ===" . PHP_EOL;
$longContent = str_repeat($cleaned . " ", 100); // Make it longer
echo "Long content length: " . strlen($longContent) . " characters" . PHP_EOL;

if (strlen($longContent) > 25000) {
    echo "Content exceeds 25000 character limit" . PHP_EOL;
    $truncated = substr($longContent, 0, 25000);
    echo "Truncated to: " . strlen($truncated) . " characters" . PHP_EOL;
} else {
    echo "Content within 25000 character limit" . PHP_EOL;
}

echo "\n=== Test Complete ===" . PHP_EOL;
