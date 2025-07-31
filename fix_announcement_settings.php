<?php
require_once 'bootstrap/app.php';

$app = $app ?? require_once __DIR__.'/bootstrap/app.php';

use App\Models\AdminSetting;

echo "Fixing announcement management settings...\n";

// Enable the feature
AdminSetting::updateOrCreate(
    ['key' => 'professor_announcement_management_enabled'],
    ['value' => '1']
);

echo "✓ Professor announcement management enabled\n";

// Check current whitelist
$whitelist = AdminSetting::getValue('professor_announcement_management_whitelist', '');
echo "Current whitelist: " . ($whitelist ?: 'EMPTY') . "\n";

// Verify the setting
$enabled = AdminSetting::getValue('professor_announcement_management_enabled', '0');
echo "Feature enabled: " . ($enabled === '1' ? 'YES' : 'NO') . "\n";

echo "Done!\n";
