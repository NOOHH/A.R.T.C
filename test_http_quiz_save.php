<?php
/**
 * Direct HTTP Test for Manual Quiz Save
 * This tests the actual endpoint via HTTP request
 */

echo "=== Direct HTTP Test for Manual Quiz Save ===\n";

// Test data matching the frontend format
$testData = [
    'title' => 'TEST QUIZ - HTTP Test',
    'description' => 'This is a test quiz created via HTTP test',
    'program_id' => '41',
    'module_id' => '79', 
    'course_id' => '53',
    'professor_id' => 8,
    'time_limit' => 60,
    'max_attempts' => 1,
    'is_draft' => true,
    'questions' => [
        [
            'question_text' => 'What is PHP?',
            'question_type' => 'multiple_choice',
            'options' => ['A scripting language', 'A database', 'A framework', 'An operating system'],
            'correct_answers' => ['A scripting language'],
            'explanation' => 'PHP is a server-side scripting language',
            'points' => 1,
            'order' => 1
        ],
        [
            'question_text' => 'Laravel is a PHP framework?',
            'question_type' => 'true_false', 
            'options' => ['True', 'False'],
            'correct_answers' => ['True'],
            'explanation' => 'Laravel is indeed a PHP web framework',
            'points' => 1,
            'order' => 2
        ]
    ]
];

echo "✓ Test data prepared\n";
echo "  - Title: " . $testData['title'] . "\n";
echo "  - Questions: " . count($testData['questions']) . "\n";
echo "  - Program ID: " . $testData['program_id'] . "\n";

// Create curl request
$url = 'http://127.0.0.1:8000/professor/quiz-generator/save-manual';

// Get CSRF token first
echo "Getting CSRF token...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/professor/quiz-generator');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$response = curl_exec($ch);

// Extract CSRF token
preg_match('/name="_token" value="([^"]+)"/', $response, $matches);
$csrfToken = $matches[1] ?? 'test_token';
echo "✓ CSRF Token: " . substr($csrfToken, 0, 20) . "...\n";

// Set professor session via direct URL (simulate login)
echo "Setting professor session...\n";
$sessionUrl = 'http://127.0.0.1:8000/set_admin_session.php';
curl_setopt($ch, CURLOPT_URL, $sessionUrl);
curl_exec($ch);

curl_close($ch);

// Add CSRF token to test data
$testData['_token'] = $csrfToken;

// Make the actual request
echo "\n--- Making HTTP POST request to save-manual endpoint ---\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "✓ Request completed\n";
echo "HTTP Code: " . $httpCode . "\n";

if ($error) {
    echo "❌ CURL Error: " . $error . "\n";
} else {
    echo "\n=== RESPONSE ===\n";
    echo "Raw Response:\n";
    echo $response . "\n";
    
    $responseData = json_decode($response, true);
    
    if ($responseData) {
        echo "\n--- Parsed Response ---\n";
        if (isset($responseData['success']) && $responseData['success']) {
            echo "✅ SUCCESS: Quiz saved successfully!\n";
            echo "Quiz ID: " . ($responseData['quiz_id'] ?? 'Not provided') . "\n";
            echo "Status: " . ($responseData['status'] ?? 'Not provided') . "\n";
            echo "Message: " . ($responseData['message'] ?? 'Not provided') . "\n";
        } else {
            echo "❌ FAILED: Quiz save failed\n";
            echo "Message: " . ($responseData['message'] ?? 'No message') . "\n";
            if (isset($responseData['errors'])) {
                echo "Validation Errors:\n";
                foreach ($responseData['errors'] as $field => $errors) {
                    echo "  - $field: " . implode(', ', $errors) . "\n";
                }
            }
        }
    } else {
        echo "❌ Invalid JSON response or no response data\n";
    }
}

// Clean up
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "\n=== Test completed ===\n";
