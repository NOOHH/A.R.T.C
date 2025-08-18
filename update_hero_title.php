<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update the hero title with a proper value
\App\Models\UiSetting::set('homepage', 'hero_title', 'Welcome to SmartPrep - Review Smarter, Learn Better, Succeed Faster!', 'text');

echo "Hero title updated successfully to database.\n";

// Verify the update
$heroTitle = \App\Models\UiSetting::get('homepage', 'hero_title', 'not found');
echo "Current hero_title in database: " . $heroTitle . "\n";

// Check what SettingsHelper returns now
$homepageContent = \App\Helpers\SettingsHelper::getHomepageContent();
echo "SettingsHelper hero_title: " . $homepageContent['hero_title'] . "\n";
