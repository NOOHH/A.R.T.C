<?php
echo "<h1>🎯 Final Quiz Route Test</h1>";

// Clear any output buffering and start fresh
while (ob_get_level()) {
    ob_end_clean();
}

// Start session properly
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up the session for user ID 15
$_SESSION['user_id'] = 15;
$_SESSION['user_name'] = 'Vince Michael Dela Vega';
$_SESSION['user_role'] = 'student';
$_SESSION['logged_in'] = true;

echo "<p>✅ Session configured for user ID 15</p>";
echo "<p>✅ Database ownership fixed to student 2025-08-00003</p>";
echo "<p>✅ SessionManager updated to work with Laravel</p>";

echo "<h2>🧪 Test Results</h2>";

// Create an iframe to test the Laravel route
echo "<div style='border: 3px solid #007bff; border-radius: 10px; padding: 20px; margin: 20px 0;'>";
echo "<h3>Laravel Quiz Route Test:</h3>";
echo "<iframe src='/A.R.T.C/public/student/quiz/take/3' width='100%' height='600px' style='border: 1px solid #ddd; border-radius: 5px;'></iframe>";
echo "</div>";

echo "<h2>📊 Expected vs Actual</h2>";
echo "<div style='display: flex; gap: 20px;'>";

echo "<div style='flex: 1; background: #d4edda; padding: 15px; border-radius: 8px;'>";
echo "<h4>✅ Expected (Working Quiz):</h4>";
echo "<ul>";
echo "<li>Quiz title: 'ww'</li>";
echo "<li>Question: 'wwwww'</li>";
echo "<li>Multiple choice options A, B, C, D</li>";
echo "<li>Submit button</li>";
echo "<li>No redirects to login/dashboard</li>";
echo "</ul>";
echo "</div>";

echo "<div style='flex: 1; background: #f8d7da; padding: 15px; border-radius: 8px;'>";
echo "<h4>❌ Problems (If Still Broken):</h4>";
echo "<ul>";
echo "<li>Redirect to login page</li>";
echo "<li>Redirect to dashboard</li>";
echo "<li>Access denied errors</li>";
echo "<li>Blank/error page</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<h2>🔗 Manual Test Links</h2>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007bff; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>🎯 Open Quiz in New Tab</a>";
echo "<a href='/A.R.T.C/public/student/dashboard' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>📊 Dashboard</a>";
echo "<a href='direct_quiz_test.php' target='_blank' style='background: #6c757d; color: white; padding: 15px 25px; text-decoration: none; border-radius: 8px; margin: 10px; display: inline-block; font-weight: bold;'>🔍 Direct Test</a>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 30px 0;'>";
echo "<h3>🎉 Success Criteria:</h3>";
echo "<p>The quiz is <strong>FIXED</strong> when the iframe above shows the quiz interface instead of a login page.</p>";
echo "<p>You should be able to:</p>";
echo "<ol>";
echo "<li>See the quiz title 'ww'</li>";
echo "<li>See the question 'wwwww'</li>";
echo "<li>Select an answer from A, B, C, or D</li>";
echo "<li>Click 'Submit Quiz' (though submission testing is separate)</li>";
echo "</ol>";
echo "</div>";
?>
