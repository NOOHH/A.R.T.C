<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set a very obvious test value
\App\Models\UiSetting::set('navbar', 'brand_name', '🔥 BRAND CHANGED SUCCESSFULLY! 🔥', 'text');

echo "✓ Set very obvious brand name: '🔥 BRAND CHANGED SUCCESSFULLY! 🔥'\n";
echo "✓ Please hard refresh the browser (Ctrl+F5) or open in incognito mode\n";
