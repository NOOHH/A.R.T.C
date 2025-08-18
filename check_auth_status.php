<?php
echo "=== AUTHENTICATION STATUS CHECK ===\n\n";

// Start session
session_start();

echo "=== SESSION DATA ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Session status: " . session_status() . "\n";
echo "Session data:\n";
print_r($_SESSION);
echo "\n";

// Check cookies
echo "=== COOKIES ===\n";
print_r($_COOKIE);
echo "\n";

// Database connection to main database
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'artc';
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ Connected to ARTC database\n\n";
    
    // Check for admin users
    echo "=== ADMIN USERS IN ARTC DATABASE ===\n";
    $stmt = $pdo->prepare("SELECT id, name, email, user_type, created_at FROM users WHERE user_type = 'admin' OR email LIKE '%admin%' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($users) {
        foreach ($users as $user) {
            echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Type: {$user['user_type']}\n";
        }
    } else {
        echo "No admin users found in ARTC database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error connecting to ARTC database: " . $e->getMessage() . "\n";
}

// Database connection to smartprep database
try {
    $database = 'smartprep';
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo2 = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "\n✅ Connected to SmartPrep database\n\n";
    
    // Check for admin users
    echo "=== ADMIN USERS IN SMARTPREP DATABASE ===\n";
    $stmt = $pdo2->prepare("SELECT id, name, email, user_type, created_at FROM users WHERE user_type = 'admin' OR email LIKE '%admin%' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($users) {
        foreach ($users as $user) {
            echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Type: {$user['user_type']}\n";
        }
    } else {
        echo "No admin users found in SmartPrep database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error connecting to SmartPrep database: " . $e->getMessage() . "\n";
}

echo "\n=== AUTH GUARD INFO ===\n";
echo "Check which Laravel guard is being used and if sessions are working properly.\n";
echo "Look at config/auth.php for guard configuration.\n";
?>
