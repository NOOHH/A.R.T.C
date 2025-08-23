<?php
echo "Testing certificates URL:\n";
$response = file_get_contents('http://127.0.0.1:8000/t/draft/test11/admin/certificates');
echo strlen($response) . " characters\n";
if (strpos($response, 'login') !== false) {
    echo "Contains login page\n";
} else {
    echo "No login detected\n";
}
echo "First 500 chars:\n";
echo substr($response, 0, 500) . "\n";
?>
