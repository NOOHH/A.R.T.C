<?php
// Test program modal data endpoint

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/search/profile?user_id=40&type=program');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$response = curl_exec($ch);
curl_close($ch);

echo "Response for program ID 40 (Nursing):\n";
echo $response . "\n\n";

// Test another program ID
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/search/profile?user_id=41&type=program');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$response = curl_exec($ch);
curl_close($ch);

echo "Response for program ID 41 (Mechanical Engineer):\n";
echo $response . "\n";
