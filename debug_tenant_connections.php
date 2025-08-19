<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Database Connection Debugging ===\n";

echo "1. Default connection: " . config('database.default') . "\n";
echo "2. MySQL DB name: " . config('database.connections.mysql.database') . "\n";
echo "3. Tenant DB name: " . config('database.connections.tenant.database') . "\n\n";

// Check hero_title in mysql connection
$mysqlTitle = DB::connection('mysql')->table('ui_settings')
    ->where('section', 'homepage')
    ->where('setting_key', 'hero_title')
    ->value('setting_value');
echo "4. Hero title from MYSQL connection: " . ($mysqlTitle ?? 'NOT FOUND') . "\n";

// Check hero_title in tenant connection (if configured)
try {
    $tenantTitle = DB::connection('tenant')->table('ui_settings')
        ->where('section', 'homepage')
        ->where('setting_key', 'hero_title')
        ->value('setting_value');
    echo "5. Hero title from TENANT connection: " . ($tenantTitle ?? 'NOT FOUND') . "\n";
} catch (Exception $e) {
    echo "5. Tenant connection error: " . $e->getMessage() . "\n";
}

// Check what UiSetting model uses
echo "\n6. What UiSetting model returns:\n";
$modelResult = App\Models\UiSetting::where('section', 'homepage')
    ->where('setting_key', 'hero_title')
    ->first();

if ($modelResult) {
    echo "   Model result: " . $modelResult->setting_value . "\n";
    echo "   Model connection: " . $modelResult->getConnectionName() . "\n";
} else {
    echo "   Model result: NOT FOUND\n";
}

// Check what UiSettingsHelper returns
echo "\n7. What UiSettingsHelper returns:\n";
$helperResult = App\Helpers\UiSettingsHelper::getSection('homepage');
$heroTitle = $helperResult['hero_title'] ?? 'NOT FOUND';
echo "   Helper result: " . $heroTitle . "\n";
