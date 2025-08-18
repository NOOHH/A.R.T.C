<?php
echo "=== AUTHENTICATION DIAGNOSIS ===\n\n";

// Database connections
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    
    // SmartPrep database
    $dsn = "mysql:host=$host;dbname=smartprep;charset=utf8mb4";
    $pdo_smartprep = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ Connected to SmartPrep database\n";
    
    // SmartPrep ARTC database
    $dsn2 = "mysql:host=$host;dbname=smartprep_artc;charset=utf8mb4";
    $pdo_artc = new PDO($dsn2, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ Connected to SmartPrep ARTC database\n\n";
    
    // Check SmartPrep admin users
    echo "=== SMARTPREP ADMIN USERS ===\n";
    $stmt = $pdo_smartprep->prepare("SELECT id, name, email, role, created_at FROM users WHERE role = 'admin' OR email LIKE '%admin%' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($users) {
        foreach ($users as $user) {
            echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}\n";
        }
    } else {
        echo "No admin users found in SmartPrep database\n";
    }
    
    echo "\n=== SMARTPREP_ARTC ADMIN USERS ===\n";
    $stmt = $pdo_artc->prepare("SELECT user_id, admin_id, email, user_firstname, user_lastname, role, created_at FROM users WHERE role = 'admin' OR role = 'super_admin' OR email LIKE '%admin%' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($users) {
        foreach ($users as $user) {
            echo "User ID: {$user['user_id']}, Admin ID: {$user['admin_id']}, Email: {$user['email']}, Name: {$user['user_firstname']} {$user['user_lastname']}, Role: {$user['role']}\n";
        }
    } else {
        echo "No admin users found in SmartPrep ARTC database\n";
    }
    
    echo "\n=== ALL SMARTPREP USERS (last 10) ===\n";
    $stmt = $pdo_smartprep->prepare("SELECT id, name, email, role FROM users ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Check if you're logged into the correct system (SmartPrep vs ARTC)\n";
echo "2. Verify which login page you used\n";
echo "3. Check browser dev tools for session/auth cookies\n";
echo "4. Clear browser cache and try logging in again\n";
?>
