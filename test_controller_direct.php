<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing HomepageController@index directly ===\n";

// Simulate what the controller does manually
$programs = App\Models\Program::where('is_archived', false)
                  ->orderBy('created_at', 'desc')
                  ->get();

$homepageSettings = App\Helpers\UiSettingsHelper::getSection('homepage')->toArray();

$homepageContent = array_merge([
    'hero_title' => 'Welcome to Ascendo Review and Training Center',
    'hero_subtitle' => 'Your premier destination for comprehensive review programs and professional training.',
    'hero_button_text' => 'ENROLL NOW',
    'programs_title' => 'Our Programs',
    'programs_subtitle' => 'Choose from our comprehensive range of review programs',
    'modalities_title' => 'Learning Modalities',
    'modalities_subtitle' => 'Flexible learning options designed to fit your schedule and learning style',
    'about_title' => 'About Us',
    'about_subtitle' => 'We are committed to providing high-quality education and training'
], $homepageSettings);

echo "Homepage Content from Controller Logic:\n";
echo "- hero_title: " . ($homepageContent['hero_title'] ?? 'NOT SET') . "\n";
echo "- hero_subtitle: " . ($homepageContent['hero_subtitle'] ?? 'NOT SET') . "\n";

echo "\nRaw UiSettingsHelper data:\n";
print_r($homepageSettings);

echo "\n=== Testing SettingsHelper::getHomepageContent() ===\n";
$settingsHelperData = App\Helpers\SettingsHelper::getHomepageContent();
echo "SettingsHelper hero_title: " . ($settingsHelperData['hero_title'] ?? 'NOT SET') . "\n";
print_r($settingsHelperData);
