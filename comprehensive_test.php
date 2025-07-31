<?php
// Comprehensive test script for quiz generation
echo "=== COMPREHENSIVE QUIZ GENERATOR TEST ===" . PHP_EOL;

// 1. Test Environment Variables
echo "\n1. Testing Environment Variables:" . PHP_EOL;
$apiKey = getenv('GEMINI_API_KEY') ?: 'AIzaSyApwLadkEmUpUe8kv5Nl5-7p35ob9_DSsY';
echo "API Key: " . ($apiKey ? "✅ Set (" . substr($apiKey, 0, 10) . "...)" : "❌ NOT SET") . PHP_EOL;

// 2. Test API directly
echo "\n2. Testing Direct API Call:" . PHP_EOL;
$payload = json_encode([
    'contents' => [['parts' => [['text' => 'Generate 1 multiple choice question about engineering.']]]],
    'generationConfig' => ['temperature' => 0.1, 'topK' => 1, 'topP' => 1, 'maxOutputTokens' => 1024]
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key={$apiKey}");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ API Call Successful" . PHP_EOL;
} else {
    echo "❌ API Call Failed: HTTP $httpCode" . PHP_EOL;
    echo "Response: $response" . PHP_EOL;
}

// 3. Test route access
echo "\n3. Testing Route Availability:" . PHP_EOL;
$routes = [
    '/professor/quiz-generator/generate-ai-questions',
    '/professor/quiz-generator',
    '/professor/dashboard'
];

foreach ($routes as $route) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000$route");
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 || $httpCode === 302) {
        echo "✅ $route - Accessible (HTTP $httpCode)" . PHP_EOL;
    } else {
        echo "❌ $route - Not accessible (HTTP $httpCode)" . PHP_EOL;
    }
}

// 4. Test file upload capability
echo "\n4. Testing File System:" . PHP_EOL;
$testFile = __DIR__ . '/storage/app/test-file.txt';
if (file_put_contents($testFile, 'Test content for quiz generation')) {
    echo "✅ File write successful" . PHP_EOL;
    unlink($testFile);
} else {
    echo "❌ File write failed" . PHP_EOL;
}

// 5. Test database connectivity
echo "\n5. Testing Database:" . PHP_EOL;
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=a_r_t_c", "root", "");
    echo "✅ Database connection successful" . PHP_EOL;
    
    // Check if quiz_questions table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'quiz_questions'");
    if ($stmt->rowCount() > 0) {
        echo "✅ quiz_questions table exists" . PHP_EOL;
    } else {
        echo "❌ quiz_questions table missing" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . PHP_EOL;
}

echo "\n=== TEST SUMMARY ===" . PHP_EOL;
echo "If all tests pass, the quiz generator should work." . PHP_EOL;
echo "Check the Laravel logs for detailed error information." . PHP_EOL;
