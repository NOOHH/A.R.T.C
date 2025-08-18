<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING COLOR CUSTOMIZATION ===\n\n";

// Set test colors for homepage
\App\Models\UiSetting::set('homepage', 'background_color', '#ff6b6b', 'color');
\App\Models\UiSetting::set('homepage', 'gradient_color', '#4ecdc4', 'color');
\App\Models\UiSetting::set('homepage', 'text_color', '#ffffff', 'color');
\App\Models\UiSetting::set('homepage', 'button_color', '#45b7d1', 'color');

echo "✓ Set test colors:\n";
echo "  Background: #ff6b6b (red)\n";
echo "  Gradient: #4ecdc4 (teal)\n";
echo "  Text: #ffffff (white)\n";
echo "  Button: #45b7d1 (blue)\n\n";

// Test what SettingsHelper returns
echo "✓ Testing SettingsHelper output:\n";
$styles = \App\Helpers\SettingsHelper::getHomepageStyles();
echo $styles . "\n\n";

echo "✓ Testing completed! Check homepage at http://127.0.0.1:8000/\n";
