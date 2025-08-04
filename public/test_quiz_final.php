<?php
echo "<h1>🔗 Quiz Route Direct Test</h1>";

// Make a direct request to the Laravel quiz route
$url = 'http://localhost/A.R.T.C/public/student/quiz/take/3';

echo "<p>Testing route: <code>$url</code></p>";

// Make the request
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false, // Don't follow redirects
    CURLOPT_TIMEOUT => 30,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    CURLOPT_HTTPHEADER => [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Connection: keep-alive',
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($curl, CURLINFO_REDIRECT_URL);

curl_close($curl);

echo "<h2>📊 Response Analysis</h2>";
echo "<p><strong>HTTP Status:</strong> $httpCode</p>";

if ($redirectUrl) {
    echo "<p><strong>Redirect URL:</strong> $redirectUrl</p>";
}

// Analyze the response content
if ($response) {
    $isLoginPage = strpos($response, 'Login') !== false || strpos($response, 'login') !== false;
    $isDashboard = strpos($response, 'dashboard') !== false || strpos($response, 'Dashboard') !== false;
    $isQuizPage = strpos($response, 'quiz') !== false && strpos($response, 'wwwww') !== false; // Contains quiz question
    
    if ($isQuizPage) {
        echo "<p>✅ <strong>Success!</strong> Quiz page loaded correctly</p>";
    } elseif ($isLoginPage) {
        echo "<p>❌ Redirected to login page - authentication failed</p>";
    } elseif ($isDashboard) {
        echo "<p>❌ Redirected to dashboard - access denied</p>";
    } else {
        echo "<p>⚠️ Unknown response type</p>";
    }
    
    echo "<h3>📄 Response Preview (first 800 chars):</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto;'>";
    echo htmlspecialchars(substr($response, 0, 800));
    echo "</div>";
} else {
    echo "<p>❌ No response received</p>";
}

echo "<br><hr>";
echo "<h2>🔗 Manual Test Links</h2>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Try Quiz Route</a>";
echo "<a href='/A.R.T.C/public/student/dashboard' target='_blank' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Dashboard</a>";
echo "<a href='debug_quiz_session.php' style='background: #6f42c1; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Debug Session</a>";
?>
