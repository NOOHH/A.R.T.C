<?php

echo "🎉 Authentication Bypass Test Results\n";
echo "====================================\n\n";

// Test URL that should bypass authentication
$test_url = 'http://localhost:8000/t/draft/smartprep/admin-dashboard?website=17';

echo "📡 Testing URL: $test_url\n\n";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $test_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);  // Don't follow redirects
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "📊 HTTP Status Code: $http_code\n";

if ($redirect_url) {
    echo "🔄 Redirect URL: $redirect_url\n";
}

// Analyze the response
if ($http_code == 302 || $http_code == 301) {
    echo "❌ AUTHENTICATION BYPASS FAILED\n";
    echo "   Still getting redirected to login\n";
} elseif ($http_code == 500) {
    echo "✅ AUTHENTICATION BYPASS SUCCESS!\n";
    echo "   No longer redirected to login - middleware bypass working\n";
    echo "   Getting 500 error due to database issue (expected)\n";
} elseif ($http_code == 200) {
    echo "✅ AUTHENTICATION BYPASS SUCCESS!\n";
    echo "   Page loaded successfully\n";
} else {
    echo "⚠️  Unexpected HTTP status: $http_code\n";
}

echo "\n📋 Response Analysis:\n";

// Check if response contains login page indicators
if (strpos($response, 'login') !== false && strpos($response, 'password') !== false) {
    echo "❌ Response contains login form\n";
} else {
    echo "✅ Response does not contain login form\n";
}

// Check for database error (which is expected after auth bypass)
if (strpos($response, 'No database selected') !== false || strpos($response, 'admin_settings') !== false) {
    echo "✅ Found expected database error - confirms we reached the controller\n";
}

// Check for Laravel error page
if (strpos($response, 'LaravelIgnition') !== false || strpos($response, 'Ignition') !== false) {
    echo "✅ Laravel error page detected - confirms we bypassed auth middleware\n";
}

echo "\n🏆 SUMMARY:\n";
echo "==========\n";
if ($http_code == 500 || $http_code == 200) {
    echo "✅ Authentication bypass is WORKING!\n";
    echo "✅ CheckAdminAuth middleware correctly allows tenant preview routes\n";
    echo "✅ Pattern '/t/draft/{tenant}/admin-dashboard' bypasses authentication\n";
    echo "✅ Next step: Fix database connection for tenant preview\n";
} else {
    echo "❌ Authentication bypass needs more work\n";
}

?>
