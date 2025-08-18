<?php
echo "=== TESTING SMARTPREP ADMIN DASHBOARD ACCESS ===\n\n";

// Test direct access to the admin dashboard URL
$dashboardUrl = 'http://localhost:8000/smartprep/admin/dashboard';
echo "Testing direct access to: $dashboardUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dashboardUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL Error: $error\n";
} else {
    echo "HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        echo "✅ Dashboard loads successfully\n";
        // Check if it actually contains dashboard content
        if (strpos($response, 'dashboard') !== false || strpos($response, 'admin') !== false) {
            echo "✅ Contains dashboard content\n";
        } else {
            echo "⚠️ Might not be the actual dashboard\n";
        }
    } elseif ($httpCode == 302) {
        echo "⚠️ Dashboard redirects (likely requires authentication)\n";
        echo "Redirect URL: $redirectUrl\n";
        
        if (strpos($redirectUrl, 'login') !== false) {
            echo "❌ Redirects back to login - authentication issue\n";
        }
    } elseif ($httpCode == 500) {
        echo "❌ Server error on dashboard page\n";
    } else {
        echo "⚠️ Unexpected HTTP code: $httpCode\n";
    }
}

echo "\n=== CHECKING MIDDLEWARE AND AUTHENTICATION ===\n";

// Let's check what middleware is applied to the admin dashboard route
try {
    // This will help us understand what's blocking access
    echo "The admin dashboard route has middleware that might be:\n";
    echo "1. Checking for admin authentication\n";
    echo "2. Requiring specific permissions\n";
    echo "3. Validating session data\n";
    echo "4. Redirecting if not properly authenticated\n\n";
    
    echo "=== POSSIBLE ISSUES ===\n";
    echo "1. 🔐 Middleware not recognizing admin guard authentication\n";
    echo "2. 🔄 Session not persisting after login\n";
    echo "3. ❌ Dashboard controller throwing an error\n";
    echo "4. 🚫 Missing permissions or roles\n";
    echo "5. 🔀 Route conflicts or wrong redirect logic\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "=== DEBUGGING STEPS ===\n";
echo "1. Check if you can access dashboard AFTER login attempt\n";
echo "2. Clear all browser data (cookies, cache, local storage)\n";
echo "3. Try login in incognito mode\n";
echo "4. Check browser Network tab during login for redirects\n";
echo "5. Look for JavaScript errors in console\n";
echo "6. Check if there are multiple session cookies conflicting\n\n";

echo "=== IMMEDIATE TEST ===\n";
echo "Try these URLs directly in browser:\n";
echo "1. http://localhost:8000/smartprep/admin/dashboard\n";
echo "2. http://localhost:8000/smartprep/login\n";
echo "3. Check what happens when you login and look at Network tab\n";
?>
