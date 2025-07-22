<?php
// Test upload script to test the /test-upload route
echo "=== TESTING /test-upload ROUTE ===\n";

// Create a test file
$testContent = "This is a test file for upload testing.";
$testFile = __DIR__ . '/temp_test_file.txt';
file_put_contents($testFile, $testContent);

echo "Created test file: $testFile\n";
echo "File size: " . filesize($testFile) . " bytes\n";

// Test upload using PHP
$url = 'http://127.0.0.1:8000/test-upload';
$cfile = new CURLFile($testFile, 'text/plain', 'test_upload.txt');

$data = [
    'attachment' => $cfile,
    '_token' => 'test' // Using test token for now
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "\n=== UPLOAD TEST RESULTS ===\n";
echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "CURL Error: $error\n";
}
echo "Response:\n";
echo $response . "\n";

// Parse JSON response
if ($httpCode === 200) {
    $jsonResponse = json_decode($response, true);
    if ($jsonResponse) {
        echo "\n=== PARSED RESPONSE ===\n";
        echo "Success: " . ($jsonResponse['success'] ? 'YES' : 'NO') . "\n";
        echo "Message: " . ($jsonResponse['message'] ?? 'N/A') . "\n";
        if (isset($jsonResponse['path'])) {
            echo "Path: " . $jsonResponse['path'] . "\n";
            echo "File exists: " . ($jsonResponse['file_exists'] ? 'YES' : 'NO') . "\n";
            echo "URL: " . ($jsonResponse['url'] ?? 'N/A') . "\n";
        }
        if (isset($jsonResponse['debug'])) {
            echo "Debug info: " . json_encode($jsonResponse['debug'], JSON_PRETTY_PRINT) . "\n";
        }
    }
}

// Clean up test file
unlink($testFile);
echo "\n🧹 Cleaned up test file.\n";
echo "=== TEST COMPLETE ===\n";
?>
