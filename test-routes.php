<?php

// Test script to verify admin settings routes work
echo "Testing admin settings routes...\n";

// Test director features route
$url = 'http://localhost:8000/admin/settings/director-features';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Director features route test:\n";
echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 200) . "...\n";

// Test professor features route
$url = 'http://localhost:8000/admin/settings/professor-features';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\nProfessor features route test:\n";
echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 200) . "...\n";

echo "\nRoute testing complete.\n";
?>
