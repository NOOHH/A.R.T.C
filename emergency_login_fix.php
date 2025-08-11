<?php
/**
 * Emergency Login Fix - Bypass validation and force login for testing
 */

echo "<h1>🚨 Emergency Login Fix</h1>";
echo "<pre>";

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "🔧 EMERGENCY LOGIN BYPASS ACTIVATED...\n\n";

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    try {
        // Bootstrap Laravel
        require_once __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle(
            $request = Illuminate\Http\Request::capture()
        );
        
        echo "✅ Laravel loaded successfully\n\n";
        
        // Get user input
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        echo "=== TESTING LOGIN FOR: $email ===\n";
        
        // Check all user tables manually
        $tables = [
            'admins' => ['email_field' => 'email', 'password_field' => 'password', 'id_field' => 'id'],
            'directors' => ['email_field' => 'directors_email', 'password_field' => 'directors_password', 'id_field' => 'directors_id'],
            'professors' => ['email_field' => 'professor_email', 'password_field' => 'professor_password', 'id_field' => 'professor_id'],
            'users' => ['email_field' => 'email', 'password_field' => 'password', 'id_field' => 'user_id']
        ];
        
        $userFound = false;
        
        foreach ($tables as $table => $fields) {
            echo "\n--- Checking $table table ---\n";
            
            $user = DB::table($table)->where($fields['email_field'], $email)->first();
            
            if ($user) {
                echo "✅ User found in $table!\n";
                echo "User ID: " . $user->{$fields['id_field']} . "\n";
                echo "Email: " . $user->{$fields['email_field']} . "\n";
                
                $storedPassword = $user->{$fields['password_field']};
                
                // Test password
                $passwordCorrect = false;
                
                // Try hashed password first
                if (Hash::check($password, $storedPassword)) {
                    $passwordCorrect = true;
                    echo "✅ Password matches (hashed)\n";
                } 
                // Try plain text
                elseif ($password === $storedPassword) {
                    $passwordCorrect = true;
                    echo "✅ Password matches (plain text)\n";
                } else {
                    echo "❌ Password does not match\n";
                    echo "First 10 chars of stored password: " . substr($storedPassword, 0, 10) . "...\n";
                }
                
                if ($passwordCorrect) {
                    echo "\n🎉 LOGIN SHOULD WORK! Forcing login session...\n";
                    
                    // Force create session based on table
                    switch ($table) {
                        case 'users':
                            session([
                                'user_id' => $user->user_id,
                                'user_name' => ($user->user_firstname ?? '') . ' ' . ($user->user_lastname ?? ''),
                                'user_email' => $user->email,
                                'user_role' => 'student',
                                'role' => 'student',
                                'logged_in' => true
                            ]);
                            $redirectUrl = '/student/dashboard';
                            break;
                            
                        case 'admins':
                            session([
                                'admin_id' => $user->id,
                                'user_id' => $user->id,
                                'user_name' => $user->name ?? 'Admin',
                                'user_email' => $user->email,
                                'user_role' => 'admin',
                                'role' => 'admin',
                                'logged_in' => true
                            ]);
                            $redirectUrl = '/admin/dashboard';
                            break;
                            
                        case 'professors':
                            session([
                                'professor_id' => $user->professor_id,
                                'user_id' => $user->professor_id,
                                'user_name' => ($user->professor_fname ?? '') . ' ' . ($user->professor_lname ?? ''),
                                'user_email' => $user->professor_email,
                                'user_role' => 'professor',
                                'role' => 'professor',
                                'logged_in' => true
                            ]);
                            $redirectUrl = '/professor/dashboard';
                            break;
                            
                        case 'directors':
                            session([
                                'director_id' => $user->directors_id,
                                'user_id' => $user->directors_id,
                                'user_name' => ($user->directors_fname ?? '') . ' ' . ($user->directors_lname ?? ''),
                                'user_email' => $user->directors_email,
                                'user_role' => 'director',
                                'role' => 'director',
                                'logged_in' => true
                            ]);
                            $redirectUrl = '/director/dashboard';
                            break;
                    }
                    
                    echo "✅ Session created successfully!\n";
                    echo "🔄 Redirecting to: $redirectUrl\n";
                    
                    // JavaScript redirect
                    echo "</pre>";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = '$redirectUrl';
                        }, 2000);
                    </script>";
                    echo "<h2>✅ LOGIN SUCCESSFUL!</h2>";
                    echo "<p>You will be redirected to your dashboard in 2 seconds...</p>";
                    echo "<p>If not redirected automatically, <a href='$redirectUrl'>click here</a></p>";
                    exit;
                }
                
                $userFound = true;
                break;
            } else {
                echo "❌ User not found in $table\n";
            }
        }
        
        if (!$userFound) {
            echo "\n❌ USER NOT FOUND IN ANY TABLE!\n";
            echo "Please check if the email is correct or if the user needs to be created.\n";
        }
        
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
    }
    
    echo "</pre>";
} else {
    echo "=== INSTRUCTIONS ===\n";
    echo "Use the form below to test emergency login\n";
    echo "This will bypass all validation and show exact issues\n";
    echo "</pre>";
}

// Emergency login form
echo "<h2>🚨 Emergency Login Form</h2>";
echo "<p><strong>This bypasses all validation and shows exact login issues</strong></p>";
echo "<form method='POST' style='border: 2px solid #dc3545; padding: 20px; max-width: 400px; background: #fff3cd;'>";
echo "    <h3>🔧 Debug Login</h3>";
echo "    <div style='margin-bottom: 15px;'>";
echo "        <label><strong>Email:</strong></label><br>";
echo "        <input type='email' name='email' required style='width: 100%; padding: 8px; margin-top: 5px;'>";
echo "    </div>";
echo "    <div style='margin-bottom: 15px;'>";
echo "        <label><strong>Password:</strong></label><br>";
echo "        <input type='password' name='password' required style='width: 100%; padding: 8px; margin-top: 5px;'>";
echo "    </div>";
echo "    <button type='submit' style='background: #dc3545; color: white; padding: 10px 20px; border: none; font-weight: bold;'>🚨 FORCE LOGIN TEST</button>";
echo "</form>";

echo "<h3>📋 What this does:</h3>";
echo "<ul>";
echo "<li>✅ Checks all user tables (users, admins, professors, directors)</li>";
echo "<li>✅ Tests both hashed and plain text passwords</li>";
echo "<li>✅ Shows exactly why login fails</li>";
echo "<li>✅ Forces login session if credentials are correct</li>";
echo "<li>✅ Redirects to appropriate dashboard</li>";
echo "</ul>";

echo "<p><strong>⚠️ This is for emergency debugging only!</strong></p>";
?>
