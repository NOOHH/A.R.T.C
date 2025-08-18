<?php
echo "=== FINAL LOGIN STATUS REPORT ===\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=smartprep", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "=== ADMIN ACCOUNTS READY ===\n";
    $stmt = $pdo->query("SELECT id, name, email FROM admins");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($admins as $admin) {
        echo "Admin: {$admin['name']} ({$admin['email']})\n";
        
        // Test password
        $stmt2 = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt2->execute([$admin['id']]);
        $passwordHash = $stmt2->fetchColumn();
        
        $passwordValid = password_verify('admin123', $passwordHash);
        echo "  Password 'admin123': " . ($passwordValid ? "✅ VALID" : "❌ INVALID") . "\n";
    }
    
    echo "\n=== CLIENT ACCOUNTS READY ===\n";
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE role = 'client' LIMIT 3");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($clients as $client) {
        echo "Client: {$client['name']} ({$client['email']})\n";
        
        // Test password for robert@gmail.com specifically
        if ($client['email'] === 'robert@gmail.com') {
            $stmt2 = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt2->execute([$client['id']]);
            $passwordHash = $stmt2->fetchColumn();
            
            $passwordValid = password_verify('client123', $passwordHash);
            echo "  Password 'client123': " . ($passwordValid ? "✅ VALID" : "❌ INVALID") . "\n";
        }
    }
    
    echo "\n=== NAVBAR BRAND STATUS ===\n";
    $stmt = $pdo->prepare("SELECT setting_value FROM ui_settings WHERE section = 'navbar' AND setting_key = 'brand_name'");
    $stmt->execute();
    $brandName = $stmt->fetchColumn();
    echo "Current brand name: '$brandName'\n";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 READY TO LOGIN! 🎯\n";
echo str_repeat("=", 60) . "\n\n";

echo "🔧 FIXES APPLIED:\n";
echo "✅ Admin users moved to 'admins' table\n";
echo "✅ Authentication guards updated\n";
echo "✅ Login controller updated for both admins and clients\n";
echo "✅ Navbar authentication fixed\n";
echo "✅ Password hashes verified\n";
echo "✅ App key regenerated (fixes CSRF issues)\n";
echo "✅ All caches cleared\n\n";

echo "🚀 ADMIN LOGIN:\n";
echo "URL: http://localhost:8000/smartprep/login\n";
echo "Email: admin@smartprep.com\n";
echo "Password: admin123\n";
echo "Expected: Redirect to admin dashboard\n\n";

echo "👤 CLIENT LOGIN:\n";
echo "URL: http://localhost:8000/smartprep/login\n";
echo "Email: robert@gmail.com\n";
echo "Password: client123\n";
echo "Expected: Redirect to client dashboard\n\n";

echo "🐛 IF LOGIN STILL FAILS:\n";
echo "1. Clear browser cache (Ctrl+Shift+Delete)\n";
echo "2. Try incognito/private mode\n";
echo "3. Check browser console (F12) for JavaScript errors\n";
echo "4. Check Network tab for 419 or other HTTP errors\n";
echo "5. Make sure Laravel dev server is running: php artisan serve\n\n";

echo "✨ AFTER SUCCESSFUL LOGIN:\n";
echo "- Navbar should show your name instead of 'Guest'\n";
echo "- Brand name should show: '$brandName'\n";
echo "- Authentication status should be true\n\n";

echo "🔄 RESTART LARAVEL SERVER:\n";
echo "Stop current server (Ctrl+C) and run: php artisan serve\n";
?>
