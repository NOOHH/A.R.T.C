<?php

// Test the SettingsHelper fix
require_once 'bootstrap/app.php';

use App\Helpers\SettingsHelper;

try {
    echo "Testing SettingsHelper::getHomepageCustomStyles()...\n";
    $styles = SettingsHelper::getHomepageCustomStyles();
    echo "✓ getHomepageCustomStyles() works!\n";
    
    echo "\nTesting SettingsHelper::getHomepageContent()...\n";
    $content = SettingsHelper::getHomepageContent();
    echo "✓ getHomepageContent() works!\n";
    
    echo "\nTesting UIHelper::getNavbarStyles()...\n";
    $navStyles = App\Helpers\UIHelper::getNavbarStyles();
    echo "✓ getNavbarStyles() works!\n";
    
    echo "\nAll tests passed! The array_merge() error should be fixed.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
