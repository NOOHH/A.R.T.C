<?php
// Simple test script to check if the modular enrollment route works
// Save this file as test-modular.php in the public directory

// Show some debug information
echo '<h1>Testing Modular Enrollment Route</h1>';
echo '<p>This page is a direct test to see if the modular enrollment route is accessible.</p>';

// Try to get the URL using Laravel's URL generator if available
if (function_exists('app') && app()->has('url')) {
    $url = app('url')->to('/enrollment/modular');
    echo "<p>Generated URL: {$url}</p>";
} else {
    $url = 'http://127.0.0.1:8000/enrollment/modular';
    echo "<p>Hardcoded URL: {$url}</p>";
}

// Provide links to test
echo "<p><a href='{$url}' target='_blank'>Click here to test direct link</a></p>";

// Provide a JavaScript redirect option
echo "<p><button onclick=\"window.location.href='{$url}'\">Test with JavaScript Redirect</button></p>";

// Provide an iframe to test embedding
echo "<p>Testing with iframe:</p>";
echo "<iframe src='{$url}' style='width:100%; height:400px; border:1px solid #ccc;'></iframe>";

// Show server and request information
echo '<h2>Server Information</h2>';
echo '<pre>';
echo 'Server: ' . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo 'PHP Version: ' . phpversion() . "\n";
echo 'Current URL: ' . $_SERVER['REQUEST_URI'] . "\n";
echo '</pre>';
?>
