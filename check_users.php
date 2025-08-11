<?php
/**
 * Quick User Database Check - Find out what users exist
 */

echo "<h1>👥 User Database Check</h1>";
echo "<pre>";

try {
    // Bootstrap Laravel
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    echo "✅ Laravel loaded successfully\n\n";
    
    // Check all user tables
    $tables = [
        'users' => ['email' => 'email', 'name' => 'user_firstname', 'id' => 'user_id'],
        'admins' => ['email' => 'email', 'name' => 'name', 'id' => 'id'],
        'professors' => ['email' => 'professor_email', 'name' => 'professor_fname', 'id' => 'professor_id'],
        'directors' => ['email' => 'directors_email', 'name' => 'directors_fname', 'id' => 'directors_id']
    ];
    
    echo "=== DATABASE USER SUMMARY ===\n\n";
    
    foreach ($tables as $tableName => $fields) {
        try {
            $count = DB::table($tableName)->count();
            echo "📊 $tableName table: $count users\n";
            
            if ($count > 0 && $count < 20) {
                echo "   Recent users:\n";
                $users = DB::table($tableName)
                    ->select($fields['email'] . ' as email', $fields['id'] . ' as id')
                    ->orderBy($fields['id'], 'desc')
                    ->limit(5)
                    ->get();
                
                foreach ($users as $user) {
                    echo "   - {$user->email} (ID: {$user->id})\n";
                }
            } elseif ($count >= 20) {
                echo "   Sample users:\n";
                $users = DB::table($tableName)
                    ->select($fields['email'] . ' as email', $fields['id'] . ' as id')
                    ->orderBy($fields['id'], 'desc')
                    ->limit(3)
                    ->get();
                
                foreach ($users as $user) {
                    echo "   - {$user->email} (ID: {$user->id})\n";
                }
                echo "   ... and " . ($count - 3) . " more\n";
            }
            echo "\n";
            
        } catch (Exception $e) {
            echo "❌ Error checking $tableName: " . $e->getMessage() . "\n\n";
        }
    }
    
    echo "=== RECOMMENDATIONS ===\n";
    echo "1. Try logging in with one of the emails shown above\n";
    echo "2. If you don't recognize any emails, you may need to create a user\n";
    echo "3. Use the emergency login tool to test specific credentials\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";

echo "<h2>🧪 Test Specific User</h2>";
echo "<p>Enter an email from the list above to test if it can login:</p>";
echo "<form method='GET' action='/emergency_login_fix.php' style='border: 1px solid #007cba; padding: 15px; max-width: 400px;'>";
echo "    <label>Email to test:</label><br>";
echo "    <input type='email' name='test_email' placeholder='user@example.com' style='width: 100%; margin: 5px 0; padding: 5px;'><br>";
echo "    <button type='submit' style='background: #007cba; color: white; padding: 8px 15px; border: none; margin-top: 5px;'>🔍 Test This User</button>";
echo "</form>";

echo "<h2>🚨 Emergency Actions</h2>";
echo "<ul>";
echo "<li><a href='/emergency_login_fix.php'>🔧 Emergency Login Fix</a> - Force login with any credentials</li>";
echo "<li><a href='/test-simple'>🧪 Test Laravel</a> - Check if Laravel is working</li>";
echo "<li><a href='/login'>🔄 Try Normal Login</a> - Go back to login page</li>";
echo "</ul>";
?>
