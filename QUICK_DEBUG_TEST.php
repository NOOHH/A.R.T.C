<?php
/**
 * QUICK DEBUG: Check if sections exist but are hidden
 */

echo "=== QUICK DEBUG TEST ===\n";

$customize_url = "http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=16";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $customize_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";

// Check for sections with style display:none or hidden
if (strpos($response, 'id="permissions-settings"') !== false) {
    echo "✅ permissions-settings ID found\n";
} else {
    echo "❌ permissions-settings ID NOT found\n";
}

if (strpos($response, 'id="director-features"') !== false) {
    echo "✅ director-features ID found\n";
} else {
    echo "❌ director-features ID NOT found\n";
}

if (strpos($response, 'id="professor-features"') !== false) {
    echo "✅ professor-features ID found\n";
} else {
    echo "❌ professor-features ID NOT found\n";
}

// Look for the specific text
if (strpos($response, 'Director Features') !== false) {
    echo "✅ 'Director Features' text found\n";
} else {
    echo "❌ 'Director Features' text NOT found\n";
}

if (strpos($response, 'Professor Features') !== false) {
    echo "✅ 'Professor Features' text found\n";
} else {
    echo "❌ 'Professor Features' text NOT found\n";
}

// Check for hidden styles
$hidden_count = substr_count($response, 'display: none');
echo "Elements with display:none: $hidden_count\n";

$hidden_count2 = substr_count($response, 'style="display: none"');
echo "Elements with style='display: none': $hidden_count2\n";

echo "\n=== END DEBUG ===\n";
?>
