<?php
echo "=== DEBUGGING ADMIN REDIRECT ISSUE ===\n\n";

// Test the admin login and redirect flow with a real HTTP request
$loginUrl = 'http://localhost:8000/smartprep/login';
$dashboardUrl = 'http://localhost:8000/smartprep/admin/dashboard';

echo "=== STEP 1: GET LOGIN FORM ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$loginPage = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    echo "❌ Failed to load login page: HTTP $httpCode\n";
    exit;
}

echo "✅ Login page loaded\n";

// Extract CSRF token
$csrfToken = '';
if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $loginPage, $matches)) {
    $csrfToken = $matches[1];
    echo "✅ CSRF token extracted: " . substr($csrfToken, 0, 10) . "...\n";
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
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "Login HTTP Code: $loginHttpCode\n";
echo "Redirect URL: $redirectUrl\n";

if ($loginHttpCode == 302 && strpos($redirectUrl, 'admin/dashboard') !== false) {
    echo "✅ Login successful, redirecting to admin dashboard\n";
    
    echo "\n=== STEP 3: FOLLOW REDIRECT TO DASHBOARD ===\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $redirectUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $dashboardResponse = curl_exec($ch);
    $dashboardHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $dashboardRedirect = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    
    echo "Dashboard HTTP Code: $dashboardHttpCode\n";
    
    if ($dashboardHttpCode == 200) {
        echo "✅ Dashboard loads successfully!\n";
        if (strpos($dashboardResponse, 'SmartPrep - Admin Dashboard') !== false) {
            echo "✅ Dashboard contains correct title\n";
        } else {
            echo "⚠️ Dashboard might not be the expected page\n";
        }
    } elseif ($dashboardHttpCode == 302) {
        echo "❌ Dashboard redirects back: $dashboardRedirect\n";
        if (strpos($dashboardRedirect, 'login') !== false) {
            echo "❌ Authentication is not persisting\n";
        }
    } else {
        echo "❌ Dashboard returned unexpected code: $dashboardHttpCode\n";
    }
    
} elseif ($loginHttpCode == 302) {
    echo "⚠️ Login redirects but not to admin dashboard: $redirectUrl\n";
} elseif ($loginHttpCode == 200) {
    echo "❌ Login returned 200 (likely validation errors)\n";
    if (strpos($loginResponse, 'error') !== false) {
        echo "Login form contains errors\n";
    }
} else {
    echo "❌ Unexpected login response: $loginHttpCode\n";
}

// Clean up
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "If login is successful but dashboard redirects back:\n";
echo "1. Check middleware authentication logic\n";
echo "2. Check session persistence\n";
echo "3. Check guard configuration\n";
echo "4. Try browser testing with Network tab open\n";
?>
