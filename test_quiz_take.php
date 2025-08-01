<?php
/**
 * Test script to verify the quiz take page loads correctly
 */

// URL for the quiz take page
$url = 'http://127.0.0.1:8000/student/quiz/attempt/10/take';

// Use cURL to make a request to the page
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=trok31vkbbhakr2hcuih525ifd; XSRF-TOKEN=eyJpdiI6Ik9vNmtQNlZYaERPNEM3ellOcWUvR2c9PSIsInZhbHVlIjoiY2pnT1BVcVBxMmxWYjdtanRrMHJud2ZSbU9Bajk1b1d2VjRLQ1VxMHMweFdRMlBqM2ZnNi8wWU1lSVdSd21SdkFqVTVCZE02cHlySndhOFBRVlNtUTBoa0tyTFgvTFZRSTE2eEttSUd6NXlLZmVKZTFhblBPM3doQlJSYXArOFkiLCJtYWMiOiI3N2E3Y2MxMWI5YzNlMDEzYjE3OGY0ZjM4ZTc5Yjk5ZTkyNTg5MTVlNmYyZjhhZjI1MjE2MDA5OWRiYmY0YjcyIiwidGFnIjoiIn0%3D; laravel_session=eyJpdiI6ImxGZDZ1U3I3aEVlRW5ENm5WWU5lbEE9PSIsInZhbHVlIjoiUWZrM3hKT3E1WXJIVjhIMXV6MWdnNkhKTHFkNzdzMm80b0VxNnA2WGJFUFBlNGZEVlJneWd0VkQzQkxZMVNpekd5bWE2VzRoSnZXekZiVUZON0N5cDQ4QnNPR0tKVitTZHRGK2xhMEQraGdYZjFIdVNzWUZqM05FUUhoU3B1cUoiLCJtYWMiOiIwMzMxMGNjOTI2MDk2YTlhZmY1OTM4ZWI0OGYyZTVjZGJjZDI3ZDVjY2FkOTllMGYxNzY2MDY1NWFhZDBiYzFjIiwidGFnIjoiIn0%3D');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check if the response contains any PHP errors
$hasError = strpos($response, 'Unsupported operand types') !== false;

echo "HTTP Status Code: " . $httpCode . "\n";
echo "Response contains 'Unsupported operand types' error: " . ($hasError ? 'Yes' : 'No') . "\n";

if ($hasError) {
    echo "Error still exists. Check the implementation.\n";
} else {
    echo "No error detected. The fix appears to be working!\n";
}

// Write the output to a file for inspection
file_put_contents(__DIR__ . '/quiz_take_test_output.html', $response);
echo "Response saved to quiz_take_test_output.html for inspection.\n";
?>
