<?php
echo "=== TESTING ACTUAL LOGIN REQUESTS ===\n\n";

// Test 1: Check if the login form loads
echo "=== TEST 1: LOGIN FORM ACCESS ===\n";
$loginUrl = 'http://localhost:8000/smartprep/login';
echo "Testing URL: $loginUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL Error: $error\n";
} else {
    echo "HTTP Status: $httpCode\n";
    if ($httpCode == 200) {
        echo "✅ Login form loads successfully\n";
        // Check if it contains login form elements
        if (strpos($response, 'email') !== false && strpos($response, 'password') !== false) {
            echo "✅ Login form contains email and password fields\n";
        } else {
            echo "⚠️ Login form might be missing form fields\n";
        }
    } else {
        echo "❌ Login form failed to load\n";
        if ($httpCode == 500) {
            echo "Server error - check Laravel logs\n";
        }
    }
}

echo "\n=== TEST 2: LOGIN SUBMISSION ===\n";

// Extract CSRF token from login form
$csrfToken = '';
if ($response && preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response, $matches)) {
    $csrfToken = $matches[1];
    echo "✅ CSRF token extracted: " . substr($csrfToken, 0, 10) . "...\n";
} else {
    echo "⚠️ Could not extract CSRF token\n";
}

// Test admin login submission
$loginSubmitUrl = 'http://localhost:8000/smartprep/login';
$adminData = [
    'email' => 'admin@smartprep.com',
    'password' => 'admin123',
    '_token' => $csrfToken
];

echo "Attempting admin login submission...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginSubmitUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($adminData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects to see what happens
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Content-Type: application/x-www-form-urlencoded'
]);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
$loginError = curl_error($ch);
curl_close($ch);

if ($loginError) {
    echo "❌ Login CURL Error: $loginError\n";
} else {
    echo "Login HTTP Status: $loginHttpCode\n";
    
    if ($loginHttpCode == 302) {
        echo "✅ Login appears successful (redirect response)\n";
        echo "Redirect URL: $redirectUrl\n";
        
        if (strpos($redirectUrl, 'admin/dashboard') !== false) {
            echo "✅ Redirecting to admin dashboard (correct)\n";
        } else {
            echo "⚠️ Unexpected redirect location\n";
        }
    } elseif ($loginHttpCode == 200) {
        echo "⚠️ Login returned 200 (might be form with errors)\n";
        if (strpos($loginResponse, 'error') !== false || strpos($loginResponse, 'invalid') !== false) {
            echo "❌ Login form shows errors\n";
        }
    } else {
        echo "❌ Unexpected HTTP code: $loginHttpCode\n";
    }
}

echo "\n=== TEST 3: DIRECT DATABASE PASSWORD CHECK ===\n";

// Final verification with direct database check
try {
    $pdo = new PDO("mysql:host=localhost;dbname=smartprep", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    $stmt = $pdo->prepare("SELECT name, email, password FROM admins WHERE email = 'admin@smartprep.com'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Admin found in database: {$admin['name']}\n";
        $passwordCheck = password_verify('admin123', $admin['password']);
        echo "Password verification: " . ($passwordCheck ? "✅ CORRECT" : "❌ WRONG") . "\n";
        
        if (!$passwordCheck) {
            echo "❌ PASSWORD MISMATCH - Let's fix this...\n";
            // Fix the password
            $correctHash = password_hash('admin123', PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE admins SET password = ? WHERE email = 'admin@smartprep.com'");
            $updateStmt->execute([$correctHash]);
            echo "✅ Password updated - try login again\n";
        }
    } else {
        echo "❌ Admin not found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== SUMMARY ===\n";
echo "If login form loads but submission fails:\n";
echo "1. Check Laravel logs: storage/logs/laravel.log\n";
echo "2. Check browser network tab for AJAX errors\n";
echo "3. Verify CSRF token is being sent\n";
echo "4. Try clearing all caches and restarting server\n";
?>
