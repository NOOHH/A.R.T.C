<?php

echo "=== ADMIN AUTHENTICATION DEBUG TEST ===\n\n";

// 1. Check PHP Session
session_start();
echo "1. PHP Session Check:\n";
echo "   - Session ID: " . session_id() . "\n";
echo "   - Session Status: " . session_status() . "\n";
echo "   - user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set') . "\n";
echo "   - user_type: " . (isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'Not set') . "\n";
echo "   - user_name: " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Not set') . "\n";
echo "   - logged_in: " . (isset($_SESSION['logged_in']) ? ($_SESSION['logged_in'] ? 'true' : 'false') : 'Not set') . "\n";
echo "   - All session data: " . json_encode($_SESSION, JSON_PRETTY_PRINT) . "\n\n";

// 2. Bootstrap Laravel for session check
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "2. Laravel Session Check:\n";
try {
    // Start Laravel session
    $app->make('session')->start();
    
    $laravelSession = $app->make('session');
    echo "   - Laravel session started: " . ($laravelSession ? 'Yes' : 'No') . "\n";
    echo "   - user_id: " . ($laravelSession->get('user_id') ?: 'Not set') . "\n";
    echo "   - user_type: " . ($laravelSession->get('user_type') ?: 'Not set') . "\n";
    echo "   - user_role: " . ($laravelSession->get('user_role') ?: 'Not set') . "\n";
    echo "   - user_name: " . ($laravelSession->get('user_name') ?: 'Not set') . "\n";
    echo "   - logged_in: " . ($laravelSession->get('logged_in') ? 'true' : 'false') . "\n";
    echo "   - All Laravel session: " . json_encode($laravelSession->all(), JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo "   - Error: " . $e->getMessage() . "\n\n";
}

// 3. Test Database User
echo "3. Database User Check:\n";
try {
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        
        // Check admin table
        $adminQuery = "SELECT * FROM admin WHERE admin_id = ?";
        $pdo = new PDO('mysql:host=localhost;dbname=artc_db', 'root', '');
        $stmt = $pdo->prepare($adminQuery);
        $stmt->execute([$userId]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            echo "   - Found in admin table: " . json_encode($admin, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "   - Not found in admin table\n";
        }
        
        // Check director table
        $directorQuery = "SELECT * FROM director WHERE director_id = ?";
        $stmt = $pdo->prepare($directorQuery);
        $stmt->execute([$userId]);
        $director = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($director) {
            echo "   - Found in director table: " . json_encode($director, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "   - Not found in director table\n";
        }
        
    } else {
        echo "   - No user_id in session to check\n";
    }
} catch (Exception $e) {
    echo "   - Database error: " . $e->getMessage() . "\n";
}

echo "\n4. Middleware Logic Simulation:\n";

// Simulate the middleware logic
$isLoggedInPHP = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$userTypePHP = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
$isAdminPHP = $userTypePHP === 'admin';
$isDirectorPHP = $userTypePHP === 'director';

echo "   - PHP Session isLoggedIn: " . ($isLoggedInPHP ? 'true' : 'false') . "\n";
echo "   - PHP Session userType: " . ($userTypePHP ?: 'null') . "\n";
echo "   - PHP Session isAdmin: " . ($isAdminPHP ? 'true' : 'false') . "\n";
echo "   - PHP Session isDirector: " . ($isDirectorPHP ? 'true' : 'false') . "\n";

// Fallback to Laravel session
if (!$isLoggedInPHP) {
    try {
        $isLoggedInLaravel = $laravelSession->get('logged_in') && $laravelSession->get('user_id');
        $userTypeLaravel = $laravelSession->get('user_role');
        $isAdminLaravel = $userTypeLaravel === 'admin';
        $isDirectorLaravel = $userTypeLaravel === 'director';
        
        echo "   - Laravel Session isLoggedIn: " . ($isLoggedInLaravel ? 'true' : 'false') . "\n";
        echo "   - Laravel Session userType: " . ($userTypeLaravel ?: 'null') . "\n";
        echo "   - Laravel Session isAdmin: " . ($isAdminLaravel ? 'true' : 'false') . "\n";
        echo "   - Laravel Session isDirector: " . ($isDirectorLaravel ? 'true' : 'false') . "\n";
    } catch (Exception $e) {
        echo "   - Laravel session error: " . $e->getMessage() . "\n";
    }
}

// Final decision
$finalLoggedIn = $isLoggedInPHP;
$finalIsAdmin = $isAdminPHP;
$finalIsDirector = $isDirectorPHP;

if (!$finalLoggedIn && isset($isLoggedInLaravel)) {
    $finalLoggedIn = $isLoggedInLaravel;
    $finalIsAdmin = $isAdminLaravel;
    $finalIsDirector = $isDirectorLaravel;
}

echo "\n5. Final Middleware Decision:\n";
echo "   - Would allow access: " . (($finalLoggedIn && ($finalIsAdmin || $finalIsDirector)) ? 'YES' : 'NO') . "\n";
echo "   - Reason: ";
if (!$finalLoggedIn) {
    echo "Not logged in\n";
} elseif (!$finalIsAdmin && !$finalIsDirector) {
    echo "Not admin or director\n";
} else {
    echo "Access granted\n";
}

echo "\n6. Recommendations:\n";
if (!$finalLoggedIn) {
    echo "   - User needs to log in properly\n";
    echo "   - Check login process sets correct session variables\n";
} elseif (!$finalIsAdmin && !$finalIsDirector) {
    echo "   - User type needs to be 'admin' or 'director'\n";
    echo "   - Current user_type: " . ($userTypePHP ?: $userTypeLaravel ?: 'null') . "\n";
    echo "   - Check user type in database and session setting\n";
} else {
    echo "   - Authentication should work\n";
    echo "   - Check CSRF token and other request issues\n";
}

echo "\n=== TEST COMPLETED ===\n";
