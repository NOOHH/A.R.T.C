<?php
// Force a request to the new quiz attempt
echo "<h1>🧪 Direct Test of Attempt #4</h1>";

// Make a fresh request to test the new attempt
$url = 'http://localhost/A.R.T.C/public/student/quiz/take/4';

echo "<p>Making direct request to: <code>$url</code></p>";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_USERAGENT => 'Mozilla/5.0 Quiz Test',
    CURLOPT_VERBOSE => true,
    CURLOPT_STDERR => fopen('php://temp', 'w+'),
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($curl, CURLINFO_REDIRECT_URL);

curl_close($curl);

echo "<h2>📊 Response Analysis</h2>";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>";

if ($redirectUrl) {
    echo "<p><strong>Redirect:</strong> $redirectUrl</p>";
}

// Analyze response
if ($response) {
    $isQuizPage = strpos($response, 'wwwww') !== false; // Contains the quiz question
    $isLoginPage = strpos($response, 'Login') !== false || strpos($response, 'login') !== false;
    $isDashboard = strpos($response, 'dashboard') !== false || strpos($response, 'Dashboard') !== false;
    
    if ($isQuizPage) {
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; color: #155724; margin: 20px 0;'>";
        echo "<h3>🎉 SUCCESS!</h3>";
        echo "<p><strong>Quiz page loaded correctly!</strong> Found quiz question 'wwwww'</p>";
        echo "</div>";
    } elseif ($isLoginPage) {
        echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; color: #721c24; margin: 20px 0;'>";
        echo "<h3>❌ FAILED</h3>";
        echo "<p>Still redirecting to login page</p>";
        echo "</div>";
    } elseif ($isDashboard) {
        echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; color: #856404; margin: 20px 0;'>";
        echo "<h3>⚠️ PARTIAL</h3>";
        echo "<p>Redirecting to dashboard (authentication works but access denied)</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #e2e3e5; padding: 20px; border-radius: 8px; color: #6c757d; margin: 20px 0;'>";
        echo "<h3>🤔 UNCLEAR</h3>";
        echo "<p>Unknown response type</p>";
        echo "</div>";
    }
    
    echo "<h3>📄 Response Preview (first 1000 chars):</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto;'>";
    echo htmlspecialchars(substr($response, 0, 1000));
    echo "</div>";
} else {
    echo "<p>❌ No response received</p>";
}

echo "<hr style='margin: 30px 0;'>";
echo "<h2>🔗 Manual Test Links</h2>";
echo "<p>Try these links directly in your browser:</p>";
echo "<a href='/A.R.T.C/public/student/quiz/take/4' target='_blank' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>🎯 Quiz Attempt #4</a>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #dc3545; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>❌ Old Attempt #3</a>";
echo "<a href='/A.R.T.C/public/student/dashboard' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>📊 Dashboard</a>";
?>
