<?php
// PHP script to check upload settings for debugging

echo "<h2>PHP Upload Settings Check</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th><th>Recommended</th></tr>";

$settings = [
    'upload_max_filesize' => ['current' => ini_get('upload_max_filesize'), 'recommended' => '100M'],
    'post_max_size' => ['current' => ini_get('post_max_size'), 'recommended' => '100M'],
    'max_file_uploads' => ['current' => ini_get('max_file_uploads'), 'recommended' => '20'],
    'max_execution_time' => ['current' => ini_get('max_execution_time'), 'recommended' => '300'],
    'max_input_time' => ['current' => ini_get('max_input_time'), 'recommended' => '300'],
    'memory_limit' => ['current' => ini_get('memory_limit'), 'recommended' => '256M'],
    'file_uploads' => ['current' => ini_get('file_uploads') ? 'On' : 'Off', 'recommended' => 'On'],
];

foreach ($settings as $setting => $values) {
    $status = '';
    if ($setting == 'file_uploads') {
        $status = $values['current'] == 'On' ? '✅' : '❌';
    } else {
        // Basic comparison - in reality you'd need to convert units
        $status = '📝'; // Just show info for now
    }
    
    echo "<tr>";
    echo "<td>{$setting}</td>";
    echo "<td>{$values['current']}</td>";
    echo "<td>{$values['recommended']} {$status}</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Upload Directory Check</h3>";
$uploadDir = storage_path('app/public');
echo "Storage directory: {$uploadDir}<br>";
echo "Writable: " . (is_writable($uploadDir) ? '✅ Yes' : '❌ No') . "<br>";

$publicStorage = public_path('storage');
echo "Public storage link: {$publicStorage}<br>";
echo "Exists: " . (file_exists($publicStorage) ? '✅ Yes' : '❌ No') . "<br>";

if (!file_exists($publicStorage)) {
    echo "<p style='color: red;'>⚠️ Storage link missing! Run 'php artisan storage:link' command.</p>";
}

// Test file upload directories
$testDirs = [
    'storage/app/public/content',
    'storage/app/public/modules'
];

foreach ($testDirs as $dir) {
    $fullPath = base_path($dir);
    echo "Directory {$dir}: ";
    if (!is_dir($fullPath)) {
        echo "❌ Does not exist";
        if (mkdir($fullPath, 0755, true)) {
            echo " (Created successfully ✅)";
        } else {
            echo " (Failed to create ❌)";
        }
    } else {
        echo "✅ Exists";
        if (is_writable($fullPath)) {
            echo " and writable ✅";
        } else {
            echo " but not writable ❌";
        }
    }
    echo "<br>";
}

echo "<p><strong>Note:</strong> Access this at <a href='http://127.0.0.1:8000/check_upload_settings.php'>http://127.0.0.1:8000/check_upload_settings.php</a></p>";
?>
