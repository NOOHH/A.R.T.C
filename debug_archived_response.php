<?php
echo "Checking what's actually being returned from archived content URL:\n\n";

$response = file_get_contents('http://127.0.0.1:8000/t/draft/test11/admin/archived');
echo "First 1000 characters:\n";
echo substr($response, 0, 1000);
echo "\n\n";

if (strpos($response, 'login') !== false) {
    echo "❌ PROBLEM: Still getting login page!\n";
    echo "This means the controller method is not being reached or is redirecting.\n";
} else {
    echo "✅ Not a login page, checking for template content...\n";
}

echo "\nLast 500 characters:\n";
echo substr($response, -500);
?>
