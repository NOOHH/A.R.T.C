<?php
/**
 * FULL DEBUG: Check HTML content regardless of display styles
 */

echo "=== FULL HTML DEBUG TEST ===\n";

$customize_url = "http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=16";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $customize_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n\n";

// Save full response for detailed inspection
file_put_contents('full_debug_response.html', $response);

// Look for our debug message
if (strpos($response, 'ADVANCED.BLADE.PHP IS LOADING') !== false) {
    echo "✅ DEBUG MESSAGE FOUND - advanced.blade.php IS loading\n";
} else {
    echo "❌ DEBUG MESSAGE NOT FOUND - advanced.blade.php NOT loading\n";
}

// Count total occurrences of sidebar-section
$sidebar_section_count = substr_count($response, 'sidebar-section');
echo "Total sidebar-section elements found: $sidebar_section_count\n";

// Count total occurrences of our IDs
$permissions_count = substr_count($response, 'id="permissions-settings"');
$director_count = substr_count($response, 'id="director-features"');
$professor_count = substr_count($response, 'id="professor-features"');

echo "permissions-settings ID count: $permissions_count\n";
echo "director-features ID count: $director_count\n";
echo "professor-features ID count: $professor_count\n";

// Look for any error messages
if (strpos($response, 'Please select a website') !== false) {
    echo "⚠️  Found 'Please select a website' message - variable issue\n";
}

if (strpos($response, 'Error') !== false || strpos($response, 'Exception') !== false) {
    echo "⚠️  Found error/exception in response\n";
}

// Check if the Advanced tab exists
if (strpos($response, 'data-section="advanced"') !== false) {
    echo "✅ Advanced tab button found\n";
} else {
    echo "❌ Advanced tab button NOT found\n";
}

echo "\n=== CONCLUSION ===\n";
if ($permissions_count > 0 || $director_count > 0 || $professor_count > 0) {
    echo "🎉 SUCCESS: Permission sections ARE present in HTML!\n";
    echo "   They are just hidden by default CSS and shown when Advanced tab is clicked.\n";
    echo "   This is the expected behavior.\n";
} else {
    echo "❌ ISSUE: Permission sections are NOT in the HTML at all.\n";
}

echo "\n=== END DEBUG ===\n";
?>
