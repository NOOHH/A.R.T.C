<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking for duplicate hero_title entries ===\n";
$allHeroTitles = App\Models\UiSetting::where('section', 'homepage')
                                    ->where('setting_key', 'hero_title')
                                    ->get();
                                    
echo "Found " . $allHeroTitles->count() . " hero_title entries:\n";
foreach ($allHeroTitles as $i => $setting) {
    echo "Entry #$i: ID=" . $setting->id . ", Value='" . $setting->setting_value . "', Created=" . $setting->created_at . "\n";
}

echo "\n=== Checking UiSettingsHelper::getSection result ===\n";
$section = App\Helpers\UiSettingsHelper::getSection('homepage');
echo "getSection result type: " . get_class($section) . "\n";
echo "Contains hero_title: " . ($section->has('hero_title') ? 'YES' : 'NO') . "\n";
if ($section->has('hero_title')) {
    echo "hero_title value: '" . $section->get('hero_title') . "'\n";
}

echo "\n=== Raw database query ===\n";
$rawResults = \Illuminate\Support\Facades\DB::table('ui_settings')
    ->where('section', 'homepage')
    ->where('setting_key', 'hero_title')
    ->get();
    
foreach ($rawResults as $result) {
    echo "Raw DB result: " . json_encode($result) . "\n";
}

echo "\n=== Check what pluck returns ===\n";
$pluckedData = App\Models\UiSetting::where('section', 'homepage')
    ->pluck('setting_value', 'setting_key');
echo "Plucked hero_title: '" . ($pluckedData['hero_title'] ?? 'NOT FOUND') . "'\n";
echo "All plucked keys: " . implode(', ', $pluckedData->keys()->toArray()) . "\n";
