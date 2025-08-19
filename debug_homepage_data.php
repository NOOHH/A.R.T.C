<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Homepage Database Values ===\n";
$settings = App\Models\UiSetting::where('section', 'homepage')->get();
foreach ($settings as $setting) {
    echo $setting->key . ': ' . $setting->value . "\n";
}

echo "\n=== UiSettingsHelper::getSection('homepage') ===\n";
$homepageData = App\Helpers\UiSettingsHelper::getSection('homepage');
print_r($homepageData->toArray());

echo "\n=== Current Homepage Controller Data ===\n";
$homepageController = new App\Http\Controllers\HomepageController();
// Let's check what the controller would return
echo "Controller exists and is accessible\n";
