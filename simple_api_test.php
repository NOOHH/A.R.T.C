<?php

// Simple API test
$apiKey = 'AIzaSyApwLadkEmUpUe8kv5Nl5-7p35ob9_DSsY';
$url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

$data = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => 'Generate one simple multiple choice question about cloud computing with 4 options.']
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 1000
    ]
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data),
        'timeout' => 30
    ]
];

echo "Testing Gemini API...\n";
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "API call failed\n";
    print_r($http_response_header);
} else {
    echo "API call successful!\n";
    $response = json_decode($result, true);
    if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
        echo "Generated text: " . $response['candidates'][0]['content']['parts'][0]['text'] . "\n";
    } else {
        echo "Unexpected response format:\n";
        print_r($response);
    }
}

?>
