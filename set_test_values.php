<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set test values
\App\Models\UiSetting::set('homepage', 'hero_title', 'TEST: Updated Hero Title from SmartPrep Settings!', 'text');
\App\Models\UiSetting::set('navbar', 'brand_name', 'TEST: SmartPrep Admin Updated', 'text');

echo "Test values set successfully!\n";

// Verify they were saved
$heroTitle = \App\Models\UiSetting::get('homepage', 'hero_title');
$brandName = \App\Models\UiSetting::get('navbar', 'brand_name');

echo "Hero title: {$heroTitle}\n";
echo "Brand name: {$brandName}\n";
