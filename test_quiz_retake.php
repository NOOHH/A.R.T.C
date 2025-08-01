<?php
// test_quiz_retake.php
// Script to test the quiz retake functionality

// Get cURL initialized
$ch = curl_init();

// Set common options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

// Function to run a request and output results
function make_request($ch, $url, $method = 'GET', $postData = null, $headers = []) {
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($method === 'POST') {
        if (is_array($postData)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        } else if (is_string($postData)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            $headers[] = 'Content-Type: application/json';
        }
    } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, null);
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "Request: $method $url\n";
    echo "Status code: $statusCode\n";
    
    return [
        'status' => $statusCode,
        'body' => $response
    ];
}

// Get CSRF token
$loginPageResponse = make_request($ch, 'http://127.0.0.1:8000/login');
preg_match('/<meta name="csrf-token" content="([^"]+)"/', $loginPageResponse['body'], $matches);
$csrfToken = $matches[1] ?? null;

if (!$csrfToken) {
    die("Could not extract CSRF token\n");
}

echo "CSRF Token: $csrfToken\n\n";

// Login as a student
echo "Testing student login...\n";
$loginResponse = make_request($ch, 'http://127.0.0.1:8000/login', 'POST', [
    '_token' => $csrfToken,
    'email' => 'student@example.com',
    'password' => 'password'
], [
    'Accept: application/json'
]);

if ($loginResponse['status'] !== 200) {
    die("Login failed\n");
}

// Get a list of quizzes
echo "\nGetting list of quizzes...\n";
$dashboardResponse = make_request($ch, 'http://127.0.0.1:8000/student/dashboard');

// Extract quiz ID from the dashboard
preg_match('/\/student\/quiz\/(\d+)\/start/', $dashboardResponse['body'], $matches);
$quizId = $matches[1] ?? null;

if (!$quizId) {
    // Try another approach to find a quiz ID
    preg_match('/quiz_id["\']?\s*:\s*["\']?(\d+)["\']?/', $dashboardResponse['body'], $matches);
    $quizId = $matches[1] ?? null;
    
    if (!$quizId) {
        die("Could not find a quiz ID\n");
    }
}

echo "Found Quiz ID: $quizId\n\n";

// Test starting the quiz
echo "Testing quiz start...\n";
$startResponse = make_request($ch, "http://127.0.0.1:8000/student/quiz/$quizId/start", 'POST', 
    json_encode([]),
    [
        'X-CSRF-TOKEN: ' . $csrfToken,
        'Accept: application/json',
        'Content-Type: application/json'
    ]
);

if ($startResponse['status'] !== 200) {
    echo "Quiz start failed with status code: " . $startResponse['status'] . "\n";
    echo "Response: " . $startResponse['body'] . "\n";
    die();
}

$startData = json_decode($startResponse['body'], true);

if (!isset($startData['success']) || !$startData['success']) {
    echo "Quiz start failed: " . ($startData['message'] ?? 'Unknown error') . "\n";
    die();
}

echo "Quiz started successfully!\n";
echo "Redirect URL: " . $startData['redirect'] . "\n\n";

// Test taking the quiz
echo "Testing quiz take...\n";
$attemptId = null;
if (preg_match('/\/student\/quiz\/take\/(\d+)/', $startData['redirect'], $matches)) {
    $attemptId = $matches[1];
    echo "Found Attempt ID: $attemptId\n";
} else {
    die("Could not extract attempt ID from redirect URL\n");
}

$takeResponse = make_request($ch, $startData['redirect']);

// Submit the quiz
echo "\nTesting quiz submission...\n";
$submitResponse = make_request($ch, "http://127.0.0.1:8000/student/quiz/$attemptId/submit", 'POST', 
    json_encode(['answers' => []]),
    [
        'X-CSRF-TOKEN: ' . $csrfToken,
        'Accept: application/json',
        'Content-Type: application/json'
    ]
);

$submitData = json_decode($submitResponse['body'], true);

if (!isset($submitData['success']) || !$submitData['success']) {
    echo "Quiz submission failed: " . ($submitData['message'] ?? 'Unknown error') . "\n";
    die();
}

echo "Quiz submitted successfully!\n";
echo "Redirect URL: " . ($submitData['redirect'] ?? 'No redirect URL') . "\n\n";

// Test viewing results
echo "Testing quiz results...\n";
if (isset($submitData['redirect'])) {
    $resultsResponse = make_request($ch, $submitData['redirect']);
    echo "Results page loaded with status: " . $resultsResponse['status'] . "\n";
    
    // Check if retake button is present
    if (strpos($resultsResponse['body'], 'retakeQuiz') !== false) {
        echo "Retake functionality is present on the results page!\n";
    } else {
        echo "Warning: Retake functionality not found on the results page.\n";
    }
} else {
    echo "No redirect URL found for results page.\n";
}

echo "\nTest completed!\n";
