<?php
echo "=== DETAILED AUTH DEBUG ===\n\n";

// Test session and authentication after login
$baseUrl = 'http://localhost:8000';
$loginUrl = $baseUrl . '/smartprep/login';
$dashboardUrl = $baseUrl . '/smartprep/admin/dashboard';

// Step 1: Get login form
echo "=== STEP 1: GET LOGIN FORM ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'auth_debug_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'auth_debug_cookies.txt');
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$loginResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    echo "❌ Failed to load login page: HTTP $httpCode\n";
    exit;
}

// Extract headers and cookies
$parts = explode("\r\n\r\n", $loginResponse, 2);
$headers = $parts[0];
$body = $parts[1];

echo "Headers received:\n";
if (preg_match_all('/Set-Cookie:\s*([^;]+)/', $headers, $matches)) {
    foreach ($matches[1] as $cookie) {
        echo "  $cookie\n";
    }
} else {
    echo "  No cookies set\n";
}

// Extract CSRF token
$csrfToken = '';
if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $body, $matches)) {
    $csrfToken = $matches[1];
    echo "✅ CSRF token extracted\n";
} else {
    echo "❌ Could not extract CSRF token\n";
    exit;
}

echo "\n=== STEP 2: SUBMIT LOGIN ===\n";
$loginData = [
    'email' => 'admin@smartprep.com',
    'password' => 'admin123',
    '_token' => $csrfToken
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'auth_debug_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'auth_debug_cookies.txt');
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$loginResponseFull = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "Login HTTP Code: $loginHttpCode\n";
echo "Redirect URL: $redirectUrl\n";

// Check what cookies were set after login
$parts = explode("\r\n\r\n", $loginResponseFull, 2);
$loginHeaders = $parts[0];

echo "Cookies set after login:\n";
if (preg_match_all('/Set-Cookie:\s*([^;]+)/', $loginHeaders, $matches)) {
    foreach ($matches[1] as $cookie) {
        echo "  $cookie\n";
    }
} else {
    echo "  No cookies set after login\n";
}

// Read current cookies from file
echo "\nCookies stored in file:\n";
if (file_exists('auth_debug_cookies.txt')) {
    $cookieContent = file_get_contents('auth_debug_cookies.txt');
    echo $cookieContent . "\n";
} else {
    echo "  No cookie file found\n";
}

if ($loginHttpCode == 302 && strpos($redirectUrl, 'admin/dashboard') !== false) {
    echo "✅ Login successful, redirecting to admin dashboard\n";
    
    echo "\n=== STEP 3: ACCESS DASHBOARD ===\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $redirectUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'auth_debug_cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'auth_debug_cookies.txt');
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $dashboardResponseFull = curl_exec($ch);
    $dashboardHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $dashboardRedirect = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    
    echo "Dashboard HTTP Code: $dashboardHttpCode\n";
    echo "Dashboard redirect: $dashboardRedirect\n";
    
    if ($dashboardHttpCode == 302 && strpos($dashboardRedirect, 'login') !== false) {
        echo "❌ Dashboard redirects back to login - authentication not working\n";
        
        echo "\n=== STEP 4: TEST AUTHENTICATION DIRECTLY ===\n";
        
        // Create a test endpoint to check auth status
        $testUrl = $baseUrl . '/smartprep/test-auth';
        
        echo "Testing authentication status...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $testUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'auth_debug_cookies.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        $authTestResponse = curl_exec($ch);
        $authTestCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Auth test HTTP Code: $authTestCode\n";
        if ($authTestCode == 200) {
            echo "Auth test response: " . substr($authTestResponse, 0, 200) . "...\n";
        }
    } else {
        echo "✅ Dashboard accessible!\n";
    }
    
} else {
    echo "❌ Login failed or unexpected redirect\n";
}

// Clean up
if (file_exists('auth_debug_cookies.txt')) {
    unlink('auth_debug_cookies.txt');
}
?>
