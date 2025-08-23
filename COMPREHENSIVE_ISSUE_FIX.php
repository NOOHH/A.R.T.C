<?php
echo "🔧 COMPREHENSIVE ISSUE FIX\n";
echo "=" . str_repeat("=", 35) . "\n\n";

// Step 1: Test the property fixes for archived modules and course upload
echo "🧪 Step 1: Testing property fixes\n";

$testUrls = [
    'archived-modules' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/modules/archived?website=1',
    'course-upload' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload?website=1'
];

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

foreach ($testUrls as $name => $url) {
    echo "Testing $name: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    if ($response !== false) {
        if (strpos($response, 'Undefined property: stdClass::$program_id') !== false) {
            echo "❌ Still has program_id error\n";
        } else {
            echo "✅ program_id error fixed\n";
        }
        
        if (strpos($response, 'Error rendering full view') === false) {
            echo "✅ View renders successfully\n";
        } else {
            echo "❌ Still has rendering errors\n";
        }
    } else {
        echo "❌ URL not accessible\n";
    }
    echo "\n";
}

echo "🔧 Step 2: Checking current status and planning modal fix\n";

// The batch upload modal is missing, so the button doesn't work
// The user is complaining that it sends them to the wrong page
// This suggests the button might be redirecting instead of opening a modal

echo "The batch upload button should open a modal for uploading course content.\n";
echo "Since the modal HTML is missing, the JavaScript fails and might cause unexpected behavior.\n";
echo "We need to:\n";
echo "1. Add the missing batch upload modal HTML\n";
echo "2. Ensure the JavaScript works correctly\n";
echo "3. Make the modal tenant-aware for preview mode\n\n";

echo "📝 Step 3: Identifying the correct modal structure\n";

// Check if there are other similar modals in the file for reference
$moduleContent = file_get_contents('resources/views/admin/admin-modules/admin-modules.blade.php');

$modalReferences = [
    'addModal' => strpos($moduleContent, 'addModal') !== false,
    'editModal' => strpos($moduleContent, 'editModal') !== false,
    'courseModal' => strpos($moduleContent, 'courseModal') !== false,
    'uploadModal' => strpos($moduleContent, 'uploadModal') !== false,
];

echo "Existing modal references in the view:\n";
foreach ($modalReferences as $modal => $exists) {
    echo "   $modal: " . ($exists ? "✅ EXISTS" : "❌ NOT FOUND") . "\n";
}

echo "\n🎯 Fix plan ready. Proceeding with implementation...\n";
?>
