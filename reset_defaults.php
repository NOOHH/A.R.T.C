<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set sensible default colors
\App\Models\UiSetting::set('homepage', 'background_color', '#667eea', 'color');
\App\Models\UiSetting::set('homepage', 'gradient_color', '#764ba2', 'color');
\App\Models\UiSetting::set('homepage', 'text_color', '#ffffff', 'color');
\App\Models\UiSetting::set('homepage', 'button_color', '#28a745', 'color');

\App\Models\UiSetting::set('homepage', 'hero_title', 'Review Smarter. Learn Better. Succeed Faster.', 'text');
\App\Models\UiSetting::set('navbar', 'brand_name', 'Ascendo Review and Training Center', 'text');

echo "✓ Reset to default settings\n";
