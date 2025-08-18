<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING NAVBAR BRAND NAME ===\n\n";

// Check what's in the database
echo "1. Database values:\n";
$navbarSettings = \App\Models\UiSetting::getSection('navbar');
$navbar = $navbarSettings ? $navbarSettings->toArray() : [];

echo "   Navbar settings from DB:\n";
foreach($navbar as $key => $value) {
    echo "      {$key} = '{$value}'\n";
}

// Check what getBrandName returns
echo "\n2. SettingsHelper::getBrandName(): " . \App\Helpers\SettingsHelper::getBrandName() . "\n";

// Test setting a new value
echo "\n3. Setting test brand name...\n";
\App\Models\UiSetting::set('navbar', 'brand_name', 'TEST BRAND NAME - Should Appear', 'text');

// Check again
$navbarSettings2 = \App\Models\UiSetting::getSection('navbar');
$navbar2 = $navbarSettings2 ? $navbarSettings2->toArray() : [];
echo "   After setting: brand_name = '" . ($navbar2['brand_name'] ?? 'NOT_FOUND') . "'\n";

// Check if there are multiple brand name fields
echo "\n4. All navbar-related settings:\n";
$allNavbarSettings = \App\Models\UiSetting::where('section', 'navbar')->get();
foreach($allNavbarSettings as $setting) {
    echo "   {$setting->setting_key} = '{$setting->setting_value}'\n";
}

echo "\n=== END DEBUG ===\n";
