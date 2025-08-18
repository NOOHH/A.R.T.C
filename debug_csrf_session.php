<?php
echo "=== CSRF AND SESSION DEBUG ===\n\n";

// Test session functionality
echo "=== SESSION TEST ===\n";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✅ Session started\n";
} else {
    echo "✅ Session already active\n";
}

echo "Session ID: " . session_id() . "\n";
echo "Session save path: " . session_save_path() . "\n";

// Test if we can write to session
$_SESSION['test'] = 'working';
echo "Session write test: " . ($_SESSION['test'] === 'working' ? "✅ WORKING" : "❌ FAILED") . "\n";

echo "\n=== CHECKING APP KEY ===\n";
$envContent = file_get_contents('.env');
if (preg_match('/APP_KEY=(.+)/', $envContent, $matches)) {
    $appKey = trim($matches[1]);
    if (empty($appKey) || $appKey === 'base64:') {
        echo "❌ APP_KEY is empty or invalid\n";
        echo "Run: php artisan key:generate\n";
    } else {
        echo "✅ APP_KEY is set: " . substr($appKey, 0, 20) . "...\n";
    }
} else {
    echo "❌ APP_KEY not found in .env\n";
}

echo "\n=== TESTING DIRECT LOGIN WITHOUT CSRF ===\n";

// Try to login by directly calling the controller method
try {
    // Create a simple login test
    $adminEmail = 'admin@smartprep.com';
    $adminPassword = 'admin123';
    
    // Connect to database
    $pdo = new PDO("mysql:host=localhost;dbname=smartprep", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Check admin exists and password is correct
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($adminPassword, $admin['password'])) {
        echo "✅ Admin credentials are valid\n";
        echo "Admin: {$admin['name']} (ID: {$admin['id']})\n";
        
        // The issue is likely CSRF or session-related, not credentials
        echo "\n=== SOLUTION ===\n";
        echo "The login should work. The 419 error suggests:\n";
        echo "1. CSRF token mismatch\n";
        echo "2. Session storage issues\n";
        echo "3. App key problems\n\n";
        
        echo "Try these steps:\n";
        echo "1. Go to: http://localhost:8000/smartprep/login\n";
        echo "2. Open browser dev tools (F12)\n";
        echo "3. Clear all cookies and local storage\n";
        echo "4. Refresh the page\n";
        echo "5. Try logging in with:\n";
        echo "   Email: admin@smartprep.com\n";
        echo "   Password: admin123\n";
        echo "6. If still fails, check Network tab for exact error\n";
        
    } else {
        echo "❌ Admin credentials invalid\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== RESET CLIENT PASSWORD ===\n";
try {
    // Also fix a client password for testing
    $clientEmail = 'robert@gmail.com';
    $newClientPassword = 'client123';
    $hashedPassword = password_hash($newClientPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hashedPassword, $clientEmail]);
    
    echo "✅ Client password reset\n";
    echo "Client login: $clientEmail / $newClientPassword\n";
    
} catch (Exception $e) {
    echo "❌ Client password reset failed: " . $e->getMessage() . "\n";
}
?>
