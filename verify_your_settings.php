<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\File;

echo "🔍 VERIFYING YOUR ACTUAL SETTINGS\n";
echo "==================================\n\n";

$settingsPath = storage_path('app/smartprep_settings.json');

if (File::exists($settingsPath)) {
    $settings = json_decode(File::get($settingsPath), true);
    
    echo "✅ Settings file found and loaded!\n\n";
    
    echo "🎯 YOUR CURRENT HOMEPAGE SETTINGS:\n";
    echo "   Hero Title: '" . $settings['homepage']['hero_title'] . "'\n";
    echo "   Hero Subtitle: '" . $settings['homepage']['hero_subtitle'] . "'\n";
    echo "   Features Title: '" . $settings['homepage']['features_title'] . "'\n\n";
    
    echo "🧭 YOUR CURRENT NAVBAR SETTINGS:\n";
    echo "   Brand Name: '" . $settings['navbar']['brand_name'] . "'\n";
    echo "   Style: '" . $settings['navbar']['style'] . "'\n\n";
    
    echo "📊 GENERAL SETTINGS:\n";
    echo "   Site Name: '" . $settings['general']['site_name'] . "'\n";
    echo "   Site Tagline: '" . $settings['general']['site_tagline'] . "'\n\n";
    
    // Simulate what the homepage view will see
    echo "🌐 WHAT WILL APPEAR ON HOMEPAGE:\n";
    echo "   Page Title: " . ($settings['general']['site_name'] ?? 'SmartPrep') . " - " . ($settings['general']['site_tagline'] ?? 'Multi-Tenant Learning Management Platform') . "\n";
    echo "   Hero Section Title: " . ($settings['homepage']['hero_title'] ?? 'Transform Education with SmartPrep') . "\n";
    echo "   Hero Section Subtitle: " . ($settings['homepage']['hero_subtitle'] ?? 'Empower your educational institution...') . "\n";
    echo "   Navbar Brand: " . ($settings['navbar']['brand_name'] ?? 'SmartPrep') . "\n";
    echo "   Primary Color: " . ($settings['branding']['primary_color'] ?? '#2563eb') . "\n\n";
    
    echo "✅ VERIFICATION COMPLETE!\n";
    echo "📍 Homepage URL: http://127.0.0.1:8000/smartprep/\n";
    echo "📍 Admin URL: http://127.0.0.1:8000/smartprep/admin/settings\n\n";
    
    if ($settings['homepage']['hero_title'] === 'sdae' && $settings['homepage']['hero_subtitle'] === 'sdsdsdsdsdsdsdsd') {
        echo "🎉 SUCCESS! Your settings match what you entered:\n";
        echo "   ✅ Hero title: 'sdae'\n";
        echo "   ✅ Hero subtitle: 'sdsdsdsdsdsdsdsd'\n";
        echo "   ✅ Brand name: 'Test Brand'\n\n";
        echo "🚀 These should now appear on your homepage at http://127.0.0.1:8000/smartprep/\n";
    } else {
        echo "❌ Settings don't match your expected values. Current values:\n";
        echo "   Hero title: '" . $settings['homepage']['hero_title'] . "'\n";
        echo "   Hero subtitle: '" . $settings['homepage']['hero_subtitle'] . "'\n";
    }
    
} else {
    echo "❌ Settings file not found at: {$settingsPath}\n";
}

echo "\n==================================\n";
