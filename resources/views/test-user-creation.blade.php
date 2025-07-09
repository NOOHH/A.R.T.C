<?php

// Simple test to check user creation without full registration
// Navigate to /test-user-creation to run this test

$testData = [
    'user_firstname' => 'Test',
    'user_lastname' => 'User',
    'email' => 'testuser_' . time() . '@example.com',
    'password' => 'testpassword123'
];

try {
    // Test database connection
    DB::connection()->getPdo();
    echo "<h3>✅ Database connection: OK</h3>";
    
    // Test User model
    $user = new \App\Models\User();
    $user->user_firstname = $testData['user_firstname'];
    $user->user_lastname = $testData['user_lastname'];
    $user->email = $testData['email'];
    $user->password = \Hash::make($testData['password']);
    $user->role = 'student';
    
    $saved = $user->save();
    
    if ($saved && $user->user_id) {
        echo "<h3>✅ User creation: SUCCESS</h3>";
        echo "<p>Created user with ID: {$user->user_id}</p>";
        echo "<p>Email: {$user->email}</p>";
        echo "<p>Name: {$user->user_firstname} {$user->user_lastname}</p>";
        
        // Clean up test user
        $user->delete();
        echo "<p>✅ Test user cleaned up</p>";
    } else {
        echo "<h3>❌ User creation: FAILED</h3>";
        echo "<p>User save returned: " . ($saved ? 'true' : 'false') . "</p>";
        echo "<p>User ID: " . ($user->user_id ?? 'null') . "</p>";
    }
    
} catch (\Exception $e) {
    echo "<h3>❌ Error: " . $e->getMessage() . "</h3>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<h3>Database Schema Check</h3>";

try {
    $columns = \Schema::getColumnListing('users');
    echo "<p><strong>Users table columns:</strong></p>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>{$column}</li>";
    }
    echo "</ul>";
} catch (\Exception $e) {
    echo "<p>Error checking schema: " . $e->getMessage() . "</p>";
}
