<?php
/**
 * Login Debug Tool - Check why login returns 200 instead of redirecting
 */

echo "<h1>🔍 Login Debug Tool</h1>";
echo "<pre>";

echo "🔍 DEBUGGING LOGIN ISSUE...\n\n";

// Check if we're in a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "=== POST LOGIN ATTEMPT ===\n";
    echo "Email: " . ($_POST['email'] ?? 'not provided') . "\n";
    echo "Password: " . (isset($_POST['password']) ? '[PROVIDED]' : 'not provided') . "\n";
    echo "Remember: " . (isset($_POST['remember']) ? 'yes' : 'no') . "\n";
    echo "CSRF Token: " . ($_POST['_token'] ?? 'not provided') . "\n\n";
}

// Test database connection and user lookup
echo "=== DATABASE CONNECTION TEST ===\n";
try {
    // Load Laravel
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    // Boot the application
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    echo "✅ Laravel application loaded\n";
    
    // Test database connection
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connected\n";
    
    // Check users table
    $userCount = DB::table('users')->count();
    echo "✅ Users table accessible - Total users: $userCount\n";
    
    // If we have POST data, test the user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
        $email = $_POST['email'];
        $user = DB::table('users')->where('email', $email)->first();
        
        if ($user) {
            echo "✅ User found in database\n";
            echo "   User ID: {$user->id}\n";
            echo "   Email: {$user->email}\n";
            echo "   Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
            
            // Test password verification
            if (isset($_POST['password'])) {
                $passwordMatches = Hash::check($_POST['password'], $user->password);
                echo "   Password Check: " . ($passwordMatches ? '✅ CORRECT' : '❌ INCORRECT') . "\n";
                
                if (!$passwordMatches) {
                    echo "   🚨 LOGIN FAILURE: Incorrect password\n";
                }
            }
        } else {
            echo "❌ User NOT found in database\n";
            echo "   🚨 LOGIN FAILURE: User doesn't exist\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== SESSION INFO ===\n";
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

echo "Session ID: " . session_id() . "\n";
echo "Session Data: " . json_encode($_SESSION) . "\n";

echo "\n=== CURRENT REQUEST ===\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'none') . "\n";
echo "User Agent: " . substr($_SERVER['HTTP_USER_AGENT'], 0, 50) . "...\n";

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. If user not found - Check user registration\n";
echo "2. If password incorrect - Reset/check password\n";
echo "3. If everything looks correct - Check login controller logic\n";

echo "</pre>";

// Simple login test form
echo "<h2>🧪 Test Login Form</h2>";
echo "<form method='POST' style='border: 1px solid #ccc; padding: 20px; max-width: 400px;'>";
echo "    <div><label>Email: <input type='email' name='email' required style='width: 100%; margin: 5px 0;'></label></div>";
echo "    <div><label>Password: <input type='password' name='password' required style='width: 100%; margin: 5px 0;'></label></div>";
echo "    <button type='submit' style='background: #007cba; color: white; padding: 10px 20px; border: none; margin-top: 10px;'>Debug Login</button>";
echo "</form>";

echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Enter your login credentials above</li>";
echo "<li>Click 'Debug Login' to see detailed debug info</li>";
echo "<li>This will show exactly why login is failing</li>";
echo "</ol>";
?>
