<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Searching for 'Review Smarter' in database ===\n";
$results = App\Models\UiSetting::where('setting_value', 'LIKE', '%Review Smarter%')->get();
foreach ($results as $result) {
    echo "Found in DB: {$result->section}.{$result->setting_key} = {$result->setting_value}\n";
}

echo "\n=== All homepage hero_title entries ===\n";
$heroTitles = App\Models\UiSetting::where('section', 'homepage')->where('setting_key', 'hero_title')->get();
foreach ($heroTitles as $title) {
    echo "hero_title in DB: {$title->setting_value}\n";
}

echo "\n=== Check if there are multiple hero title entries ===\n";
$allHeroKeys = App\Models\UiSetting::where('section', 'homepage')->where('setting_key', 'LIKE', '%hero%')->where('setting_key', 'LIKE', '%title%')->get();
foreach ($allHeroKeys as $entry) {
    echo "Hero title key: {$entry->setting_key} = {$entry->setting_value}\n";
}

echo "\n=== Raw SQL search in ui_settings table ===\n";
try {
    $results = \Illuminate\Support\Facades\DB::select("SELECT * FROM ui_settings WHERE setting_value LIKE '%Review Smarter%'");
    foreach ($results as $row) {
        echo "Found in ui_settings: " . json_encode($row) . "\n";
    }
} catch (Exception $e) {
    echo "SQL search failed: " . $e->getMessage() . "\n";
}
