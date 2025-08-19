<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test API endpoint for professor sidebar
echo "<h2>Testing Professor Sidebar API</h2>";

$settings = App\Models\UiSetting::where('section', 'professor_sidebar')->get();
echo "<h3>Database Settings:</h3>";
foreach($settings as $setting) {
    echo "<p>{$setting->setting_key}: {$setting->setting_value}</p>";
}

// Test the helper method
echo "<h3>SettingsHelper CSS:</h3>";
$css = App\Helpers\SettingsHelper::getSidebarCSS('professor');
echo "<pre>" . htmlspecialchars($css) . "</pre>";

echo "<h3>SettingsHelper Inline CSS:</h3>";
$inlineCss = App\Helpers\SettingsHelper::getSidebarInlineCSS('professor');
echo "<pre>" . htmlspecialchars(json_encode($inlineCss, JSON_PRETTY_PRINT)) . "</pre>";

// Test the controller method by simulating a request
echo "<h3>Controller API Response:</h3>";
try {
    $request = new Illuminate\Http\Request(['role' => 'professor']);
    $controller = new App\Http\Controllers\Smartprep\Dashboard\ClientDashboardController();
    $response = $controller->getSidebarSettings($request);
    $data = $response->getData();
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
