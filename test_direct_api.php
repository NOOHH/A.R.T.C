<?php
/**
 * Direct API Test with Professor Session
 * This test will simulate the exact API call with proper authentication
 */

echo "=== Direct API Test ===\n";

// Set up session file with professor authentication
$sessionData = [
    'professor_id' => 8,
    'logged_in' => true,
    'user_role' => 'professor',
    'user_type' => 'professor',
    'user_id' => 8,
    '_token' => 'test_csrf_token'
];

// Create temporary session file
$sessionId = 'test_session_' . time();
$sessionFile = sys_get_temp_dir() . '/sess_' . $sessionId;
file_put_contents($sessionFile, serialize($sessionData));

echo "✓ Session file created: $sessionFile\n";

// Test data exactly as frontend sends it
$testData = [
    'title' => 'API Test Quiz',
    'description' => 'Testing via direct API call',
    'program_id' => '41',
    'module_id' => '79',
    'course_id' => '53',
    'professor_id' => 8,
    'time_limit' => 60,
    'max_attempts' => 1,
    'is_draft' => true,
    'questions' => [
        [
            'question_text' => 'What is Laravel?',
            'question_type' => 'multiple_choice',
            'options' => ['A PHP framework', 'A database', 'A programming language', 'An IDE'],
            'correct_answers' => ['A PHP framework'],
            'explanation' => 'Laravel is a web application framework with elegant syntax',
            'points' => 1,
            'order' => 1
        ],
        [
            'question_text' => 'PHP stands for PHP: Hypertext Preprocessor?',
            'question_type' => 'true_false',
            'options' => ['True', 'False'],
            'correct_answers' => ['True'],
            'explanation' => 'PHP is a recursive acronym',
            'points' => 1,
            'order' => 2
        ]
    ],
    '_token' => 'test_csrf_token'
];

echo "✓ Test data prepared with " . count($testData['questions']) . " questions\n";

// Debug: Print the exact data structure
echo "\n--- Request Data Structure ---\n";
foreach ($testData as $key => $value) {
    if ($key === 'questions') {
        echo "$key: array with " . count($value) . " items\n";
        foreach ($value as $i => $question) {
            echo "  Question $i:\n";
            foreach ($question as $qKey => $qValue) {
                if (is_array($qValue)) {
                    echo "    $qKey: array(" . implode(', ', $qValue) . ")\n";
                } else {
                    echo "    $qKey: $qValue\n";
                }
            }
        }
    } else {
        echo "$key: $value\n";
    }
}

// Make the request with proper session cookie
$url = 'http://127.0.0.1:8000/professor/quiz-generator/save-manual';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest',
    'X-CSRF-TOKEN: test_csrf_token',
    'Cookie: laravel_session=' . $sessionId
]);
curl_setopt($ch, CURLOPT_VERBOSE, true);

echo "\n--- Making API Request ---\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "✓ Request completed\n";
echo "HTTP Status Code: $httpCode\n";

if ($error) {
    echo "❌ CURL Error: $error\n";
} else {
    echo "\n=== RESPONSE ===\n";
    echo "Raw Response:\n$response\n";
    
    // Try to parse JSON response
    $responseData = json_decode($response, true);
    
    if ($responseData) {
        echo "\n--- Parsed Response ---\n";
        if (isset($responseData['success'])) {
            if ($responseData['success']) {
                echo "✅ SUCCESS!\n";
                echo "Message: " . ($responseData['message'] ?? 'No message') . "\n";
                echo "Quiz ID: " . ($responseData['quiz_id'] ?? 'Not provided') . "\n";
            } else {
                echo "❌ FAILED\n";
                echo "Message: " . ($responseData['message'] ?? 'No message') . "\n";
                
                if (isset($responseData['errors'])) {
                    echo "\nValidation Errors:\n";
                    foreach ($responseData['errors'] as $field => $fieldErrors) {
                        echo "  $field:\n";
                        foreach ($fieldErrors as $error) {
                            echo "    - $error\n";
                        }
                    }
                }
            }
        } else {
            echo "❌ Unexpected response format\n";
            print_r($responseData);
        }
    } else {
        echo "❌ Could not parse JSON response\n";
        echo "Response might be HTML error page\n";
    }
}

// Clean up
if (file_exists($sessionFile)) {
    unlink($sessionFile);
    echo "\n✓ Session file cleaned up\n";
}

echo "\n=== API Test Completed ===\n";
