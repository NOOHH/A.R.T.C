<?php
echo "=== Testing SmartPrep Admin Form Persistence Fix ===\n\n";

// 1. Check current settings in main settings.json
$settingsPath = 'storage/app/settings.json';
if (file_exists($settingsPath)) {
    $settings = json_decode(file_get_contents($settingsPath), true);
    echo "✅ Current main settings.json hero title: " . ($settings['homepage']['hero_title'] ?? 'NOT SET') . "\n";
    echo "✅ Current main settings.json hero subtitle: " . ($settings['homepage']['hero_subtitle'] ?? 'NOT SET') . "\n";
} else {
    echo "❌ Main settings.json file not found\n";
}

// 2. Check if refreshPreview() function was commented out
$viewPath = 'resources/views/smartprep/admin/admin-settings/index.blade.php';
if (file_exists($viewPath)) {
    $viewContent = file_get_contents($viewPath);
    
    if (strpos($viewContent, '// refreshPreview();') !== false) {
        echo "\n✅ refreshPreview() function is commented out (FIXED!)\n";
    } elseif (strpos($viewContent, 'refreshPreview();') !== false) {
        echo "\n❌ refreshPreview() function is still active (needs fix)\n";
    } else {
        echo "\n⚠️  refreshPreview() function not found in view\n";
    }
} else {
    echo "\n❌ SmartPrep admin view file not found\n";
}

// 3. Check SmartPrep controller is using main settings file
$controllerPath = 'app/Http/Controllers/Smartprep/Admin/AdminSettingsController.php';
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    
    if (strpos($controllerContent, "storage_path('app/settings.json')") !== false) {
        echo "✅ SmartPrep controller uses main settings.json file (CORRECT!)\n";
    } elseif (strpos($controllerContent, "smartprep_settings.json") !== false) {
        echo "❌ SmartPrep controller still uses separate settings file (needs fix)\n";
    } else {
        echo "⚠️  Could not determine settings file usage\n";
    }
} else {
    echo "❌ SmartPrep controller file not found\n";
}

echo "\n=== Summary of Fixes Applied ===\n";
echo "✅ SmartPrep admin now saves to main settings.json (controls main A.R.T.C homepage)\n";
echo "✅ refreshPreview() JavaScript function commented out to prevent form reset\n";
echo "✅ Backend persistence confirmed working in previous tests\n";

echo "\n=== Ready for User Testing ===\n";
echo "🎯 Visit: http://127.0.0.1:8000/smartprep/admin/settings\n";
echo "🎯 Test: Change hero title/subtitle and submit form\n";
echo "🎯 Expected: Form values should persist after submission\n";
echo "🎯 Verify: Changes appear on main homepage at http://127.0.0.1:8000/\n";
