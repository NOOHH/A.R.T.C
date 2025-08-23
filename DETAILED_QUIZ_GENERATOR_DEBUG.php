<?php
echo "🔍 DETAILED QUIZ-GENERATOR DEBUG TEST\n";
echo "=" . str_repeat("=", 45) . "\n\n";

// Test the quiz-generator route with detailed error capture
$url = 'http://127.0.0.1:8000/t/draft/smartprep/admin/quiz-generator?website=1';

$context = stream_context_create([
    'http' => [
        'timeout' => 15,
        'ignore_errors' => true
    ]
]);

echo "🎯 Testing quiz-generator route with detailed error capture...\n";
echo "URL: $url\n\n";

$response = @file_get_contents($url, false, $context);

if ($response !== false) {
    echo "✅ Route responded\n\n";
    
    // Look for any error messages in the response
    if (strpos($response, 'Error rendering full view:') !== false) {
        echo "🚨 Found error in response:\n";
        
        // Extract the specific error message
        if (preg_match('/Error rendering full view: (.+?)(?=\s*\(View:|$)/s', $response, $matches)) {
            $errorMsg = trim($matches[1]);
            echo "❌ ERROR: $errorMsg\n\n";
            
            // Check if it's the admin_settings error
            if (strpos($errorMsg, 'admin_settings') !== false) {
                echo "🎯 This is the admin_settings database error!\n";
                echo "   The admin-dashboard-layout.blade.php is still trying to query admin_settings\n";
                echo "   but the database connection is back to main database.\n\n";
                
                // Check if our fix was applied
                echo "📝 Checking if our fix was applied to admin-dashboard-layout.blade.php...\n";
                $layoutContent = file_get_contents('resources/views/admin/admin-dashboard/admin-dashboard-layout.blade.php');
                
                if (strpos($layoutContent, "AdminSetting::getValue('director_view_students'") !== false) {
                    echo "❌ FIX NOT APPLIED! Still using AdminSetting::getValue\n";
                    echo "   The layout file still contains the problematic code.\n\n";
                    
                    // Show the exact line
                    $lines = explode("\n", $layoutContent);
                    foreach ($lines as $lineNum => $line) {
                        if (strpos($line, "AdminSetting::getValue('director_view_students'") !== false) {
                            echo "   Problem line " . ($lineNum + 1) . ": " . trim($line) . "\n";
                        }
                    }
                } else {
                    echo "✅ Fix was applied - AdminSetting::getValue replaced\n";
                    
                    // Check if adminSettings is being used instead
                    if (strpos($layoutContent, '$adminSettings') !== false) {
                        echo "✅ Layout now uses \$adminSettings variable\n";
                    } else {
                        echo "⚠️  Layout doesn't seem to use \$adminSettings either\n";
                    }
                }
            }
        }
    } else {
        echo "✅ No error messages found in response\n";
        
        // Check if the page loaded successfully
        if (strpos($response, 'Quiz Generator') !== false || strpos($response, 'working correctly') !== false) {
            echo "✅ Page appears to have loaded successfully\n";
        } else {
            echo "⚠️  Unexpected response content\n";
            echo "Response preview: " . substr($response, 0, 200) . "...\n";
        }
    }
} else {
    echo "❌ Route failed to respond\n";
}

echo "\n🧪 ADDITIONAL DEBUG INFO:\n";

// Check the current state of the admin dashboard layout
echo "📄 Current admin-dashboard-layout.blade.php status:\n";
$layoutContent = file_get_contents('resources/views/admin/admin-dashboard/admin-dashboard-layout.blade.php');

$hasAdminSetting = strpos($layoutContent, "AdminSetting::getValue") !== false;
$hasAdminSettings = strpos($layoutContent, '$adminSettings') !== false;

echo "   - Contains AdminSetting::getValue: " . ($hasAdminSetting ? "❌ YES" : "✅ NO") . "\n";
echo "   - Contains \$adminSettings: " . ($hasAdminSettings ? "✅ YES" : "❌ NO") . "\n";

// Check the trait
echo "\n📄 Current AdminPreviewCustomization trait status:\n";
$traitContent = file_get_contents('app/Http/Controllers/Traits/AdminPreviewCustomization.php');

$hasDirectorViewStudents = strpos($traitContent, 'director_view_students') !== false;
$hasAdminSettingsShare = strpos($traitContent, "view()->share('adminSettings'") !== false;

echo "   - Contains director_view_students logic: " . ($hasDirectorViewStudents ? "✅ YES" : "❌ NO") . "\n";
echo "   - Shares adminSettings with view: " . ($hasAdminSettingsShare ? "✅ YES" : "❌ NO") . "\n";

echo "\n🎯 Debug complete!\n";
?>
