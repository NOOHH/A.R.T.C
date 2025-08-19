<?php
require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UiSetting;

// Test if we can set and get homepage colors
echo "Testing Homepage Color Settings...\n\n";

// Set some test colors
UiSetting::set('homepage', 'primary_color', '#ff0000', 'color');
UiSetting::set('homepage', 'secondary_color', '#00ff00', 'color');
UiSetting::set('homepage', 'background_color', '#0000ff', 'color');

echo "Set test colors:\n";
echo "- Primary: #ff0000 (red)\n";
echo "- Secondary: #00ff00 (green)\n";
echo "- Background: #0000ff (blue)\n\n";

// Get the colors back
$primaryColor = UiSetting::get('homepage', 'primary_color', '#667eea');
$secondaryColor = UiSetting::get('homepage', 'secondary_color', '#764ba2');
$backgroundColor = UiSetting::get('homepage', 'background_color', '#667eea');

echo "Retrieved colors:\n";
echo "- Primary: $primaryColor\n";
echo "- Secondary: $secondaryColor\n";
echo "- Background: $backgroundColor\n\n";

// Reset to defaults
UiSetting::set('homepage', 'primary_color', '#667eea', 'color');
UiSetting::set('homepage', 'secondary_color', '#764ba2', 'color');
UiSetting::set('homepage', 'background_color', '#667eea', 'color');

echo "Reset to default colors.\n";
echo "Homepage color system is working!\n";
?>
