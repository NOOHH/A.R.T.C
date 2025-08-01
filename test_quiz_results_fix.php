<?php
/**
 * Test script to verify the quiz results page loads correctly after the fix
 * This checks if the "Unsupported operand types" error has been resolved
 */

// URL for the quiz results page - update with a valid attempt ID if needed
$url = 'http://127.0.0.1:8000/student/quiz/attempt/1/results';

// Use cURL to make a request to the quiz results page
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '');
curl_setopt($ch, CURLOPT_COOKIEJAR, '');

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check the HTTP status code
echo "HTTP Status Code: " . $httpCode . "\n";

// Check if the response contains any error messages related to our fix
$errorCheck = strpos($response, 'Unsupported operand types') !== false;
echo "Response contains 'Unsupported operand types' error: " . ($errorCheck ? 'Yes' : 'No') . "\n";

// Output overall result
if ($httpCode == 200 && !$errorCheck) {
    echo "No error detected. The fix appears to be working!\n";
} else {
    echo "Issues may still exist. Please check the response content for details.\n";
}

// Save the response to a file for inspection if needed
file_put_contents('quiz_results_test_output.html', $response);
echo "Response saved to quiz_results_test_output.html for inspection.\n";
