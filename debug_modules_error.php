<?php
$url = 'http://127.0.0.1:8000/t/draft/test11/admin/modules?website=15&preview=true';
$response = file_get_contents($url);

echo "Checking for errors in modules page...\n";

if (strpos($response, 'Error:') !== false) {
    preg_match('/Error:([^<]+)/', $response, $matches);
    if (isset($matches[1])) {
        echo 'Error found: ' . trim($matches[1]) . "\n";
    }
}

if (strpos($response, 'Undefined') !== false) {
    preg_match('/Undefined[^<]+/', $response, $matches);
    if (isset($matches[0])) {
        echo 'Undefined error: ' . trim($matches[0]) . "\n";
    }
}

if (strpos($response, 'does not exist') !== false) {
    preg_match('/[^<]*does not exist[^<]*/', $response, $matches);
    if (isset($matches[0])) {
        echo 'Method error: ' . trim($matches[0]) . "\n";
    }
}

echo "Check complete.\n";
?>
