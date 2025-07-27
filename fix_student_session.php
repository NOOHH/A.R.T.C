<?php
// Fix student session script
session_start();

echo "<h2>Current Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Clearing session and setting as student...</h2>";

// Clear all session data
session_unset();
session_destroy();

// Start fresh session
session_start();

// Set student session data (using the correct user_id from test files)
session([
    'logged_in' => true,
    'user_id' => 179, // This is the correct student user_id from test files
    'user_role' => 'student',
    'user_name' => 'robert san',
    'user_firstname' => 'robert',
    'user_lastname' => 'san',
    'user_email' => 'robert@example.com' // Replace with actual email if needed
]);

echo "<h2>New Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Testing Authentication:</h2>";
echo "User ID: " . session('user_id') . "<br>";
echo "User Role: " . session('user_role') . "<br>";
echo "User Name: " . session('user_name') . "<br>";
echo "Logged In: " . (session('logged_in') ? 'Yes' : 'No') . "<br>";

echo "<h2>Next Steps:</h2>";
echo "<p>✅ Session has been set correctly as a student</p>";
echo "<p>✅ Now try accessing the search functionality again</p>";
echo "<p>✅ The navbar should show you as a student with proper student navigation</p>";
echo "<p>✅ Program profile pages should work correctly</p>";

echo "<p><a href='/profile/program/1' target='_blank'>Test Program Profile Page</a></p>";
echo "<p><a href='/' target='_blank'>Go to Homepage</a></p>";
echo "<p><a href='/student/dashboard' target='_blank'>Go to Student Dashboard</a></p>";

echo "<h2>JavaScript Console Check:</h2>";
echo "<p>Open browser console and check if these variables are correct:</p>";
echo "<ul>";
echo "<li>myId: should be 179</li>";
echo "<li>myName: should be 'robert san'</li>";
echo "<li>userRole: should be 'student'</li>";
echo "<li>isAuthenticated: should be true</li>";
echo "</ul>";
?> 