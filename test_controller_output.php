<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing HomepageController directly ===\n";

// Simulate the controller logic
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

echo "Homepage Content being passed to view:\n";
echo "hero_title: " . $homepageContent['hero_title'] . "\n";
echo "hero_subtitle: " . $homepageContent['hero_subtitle'] . "\n";

echo "\n=== Raw UiSettingsHelper output ===\n";
print_r($homepageSettings);
