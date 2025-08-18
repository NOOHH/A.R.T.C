<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING HOMEPAGE DISPLAY ISSUE ===\n\n";

// Check what SettingsHelper returns
$content = \App\Helpers\SettingsHelper::getHomepageContent();
echo "1. SettingsHelper::getHomepageContent():\n";
echo "   hero_title: " . $content['hero_title'] . "\n";
echo "   hero_subtitle: " . $content['hero_subtitle'] . "\n\n";

$brandName = \App\Helpers\SettingsHelper::getBrandName();
echo "2. SettingsHelper::getBrandName(): " . $brandName . "\n\n";

// Check database directly
echo "3. Direct database check:\n";
$heroTitle = \App\Models\UiSetting::get('homepage', 'hero_title', 'NOT_FOUND');
$navbarBrand = \App\Models\UiSetting::get('navbar', 'brand_name', 'NOT_FOUND');

echo "   Database hero_title: " . $heroTitle . "\n";
echo "   Database navbar brand_name: " . $navbarBrand . "\n\n";

// Check if the homepage is using the right data source
echo "4. Checking SettingsHelper logic...\n";
$settings = \App\Helpers\SettingsHelper::getSettings();
echo "   JSON file homepage hero_title: " . ($settings['homepage']['hero_title'] ?? 'NOT_SET') . "\n";
echo "   JSON file navbar brand_name: " . ($settings['navbar']['brand_name'] ?? 'NOT_SET') . "\n\n";

echo "=== END CHECK ===\n";
