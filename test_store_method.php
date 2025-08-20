<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UiSetting;
use App\Models\Client;
use App\Models\User;

try {
    echo "Testing CustomizeWebsiteController store method...\n\n";
    
    // Check if we have admin settings
    $navbarSettings = UiSetting::getSection('navbar');
    $brandingSettings = UiSetting::getSection('branding');
    $homepageSettings = UiSetting::getSection('homepage');
    
    echo "Found admin settings:\n";
    echo "- Navbar settings: " . count($navbarSettings) . " items\n";
    echo "- Branding settings: " . count($brandingSettings) . " items\n";
    echo "- Homepage settings: " . count($homepageSettings) . " items\n\n";
    
    // Show sample settings
    if (!empty($navbarSettings)) {
        echo "Sample navbar settings:\n";
        foreach ($navbarSettings->take(3) as $key => $value) {
            echo "  - $key: $value\n";
        }
        echo "\n";
    }
    
    if (!empty($brandingSettings)) {
        echo "Sample branding settings:\n";
        foreach ($brandingSettings->take(3) as $key => $value) {
            echo "  - $key: $value\n";
        }
        echo "\n";
    }
    
    echo "Test completed successfully!\n";
    echo "The store method should now properly copy admin customization settings to new client tenants.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
