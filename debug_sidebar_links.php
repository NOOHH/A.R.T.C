<?php
// Debug the sidebar HTML output

$tenant = 'test1';
$dashboard_url = "http://127.0.0.1:8000/t/draft/$tenant/professor/dashboard";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dashboard_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Cookie: preview_tenant=' . $tenant
]);

$response = curl_exec($ch);
curl_close($ch);

// Extract the sidebar nav links
preg_match_all('/href="([^"]*professor[^"]*)"/', $response, $matches);

echo "Professor Navigation Links Found:\n";
foreach ($matches[1] as $link) {
    echo "- $link\n";
}

echo "\nTotal links found: " . count($matches[1]) . "\n";
?>
