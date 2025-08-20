<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Current Sidebar Settings ===\n";
$sidebarSettings = App\Models\UiSetting::whereIn('section', ['student_sidebar', 'professor_sidebar', 'admin_sidebar'])
    ->get(['section', 'setting_key', 'setting_value']);

foreach ($sidebarSettings as $setting) {
    echo "{$setting->section}.{$setting->setting_key} = {$setting->setting_value}\n";
}

echo "\n=== All UI Settings Sections ===\n";
$allSections = App\Models\UiSetting::distinct()->pluck('section');
foreach ($allSections as $section) {
    echo "- {$section}\n";
}
