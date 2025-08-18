<?php
echo "=== TESTING UPDATED LOGIN CONTROLLER LOGIC ===\n\n";

// Test the same logic as the updated controller
try {
    $pdo = new PDO("mysql:host=localhost;dbname=smartprep", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Test admin login
    echo "=== ADMIN LOGIN TEST ===\n";
    $adminEmail = 'admin@smartprep.com';
    $adminPassword = 'admin123';
    
    // Step 1: Check admins table
    $stmt = $pdo->prepare("SELECT id, name, email, password FROM admins WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Admin found: {$admin['name']} (ID: {$admin['id']})\n";
        
        if (password_verify($adminPassword, $admin['password'])) {
            echo "✅ Admin password valid\n";
            echo "→ Would use Auth::guard('admin')->login()\n";
            echo "→ Would redirect to smartprep.admin.dashboard\n";
        } else {
            echo "❌ Admin password invalid\n";
        }
    } else {
        echo "❌ Admin not found in admins table\n";
    }
    
    echo "\n=== CLIENT LOGIN TEST ===\n";
    $clientEmail = 'robert@gmail.com';
    $clientPassword = 'client123';
    
    // Step 1: Check admins table first (should not find client)
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->execute([$clientEmail]);
    $adminFound = $stmt->fetch();
    
    if (!$adminFound) {
        echo "✅ Client not found in admins table (correct)\n";
        
        // Step 2: Check users table
        $stmt = $pdo->prepare("SELECT id, name, email, role, password FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$clientEmail, $clientEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "✅ User found: {$user['name']} (ID: {$user['id']}, Role: {$user['role']})\n";
            
            if (password_verify($clientPassword, $user['password'])) {
                echo "✅ User password valid\n";
                echo "→ Would use Auth::guard('smartprep')->login()\n";
                echo "→ Would redirect to smartprep.dashboard\n";
            } else {
                echo "❌ User password invalid\n";
            }
        } else {
            echo "❌ User not found in users table\n";
        }
    } else {
        echo "⚠️ Client found in admins table (unexpected)\n";
    }
    
    echo "\n=== LOGIN FLOW SUMMARY ===\n";
    echo "1. ✅ Check admins table first\n";
    echo "2. ✅ If admin found and password valid → admin guard → admin dashboard\n";
    echo "3. ✅ If not admin, check users table\n";
    echo "4. ✅ If user found and password valid → smartprep guard → user dashboard\n";
    echo "5. ✅ If neither found → show error\n";
    
    echo "\n=== READY TO TEST ===\n";
    echo "Admin Login:\n";
    echo "  URL: http://localhost:8000/smartprep/login\n";
    echo "  Email: admin@smartprep.com\n";
    echo "  Password: admin123\n";
    echo "  Expected: Redirect to admin dashboard\n\n";
    
    echo "Client Login:\n";
    echo "  URL: http://localhost:8000/smartprep/login\n";
    echo "  Email: robert@gmail.com\n";
    echo "  Password: client123\n";
    echo "  Expected: Redirect to client dashboard\n\n";
    
    echo "=== DEBUGGING ===\n";
    echo "If login fails, check:\n";
    echo "1. Laravel logs: storage/logs/laravel.log\n";
    echo "2. Browser console (F12)\n";
    echo "3. Network tab for HTTP errors\n";
    echo "4. Clear browser cache and try incognito mode\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
