<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set professor sidebar colors for testing
App\Models\UiSetting::updateOrCreate(
    ['section' => 'professor_sidebar', 'setting_key' => 'primary_color'],
    ['setting_value' => '#6A0DAD', 'setting_type' => 'color'] // Purple
);

App\Models\UiSetting::updateOrCreate(
    ['section' => 'professor_sidebar', 'setting_key' => 'secondary_color'],
    ['setting_value' => '#9932CC', 'setting_type' => 'color'] // Dark Orchid
);

App\Models\UiSetting::updateOrCreate(
    ['section' => 'professor_sidebar', 'setting_key' => 'accent_color'],
    ['setting_value' => '#BA55D3', 'setting_type' => 'color'] // Medium Orchid
);

App\Models\UiSetting::updateOrCreate(
    ['section' => 'professor_sidebar', 'setting_key' => 'text_color'],
    ['setting_value' => '#FFFFFF', 'setting_type' => 'color'] // White
);

App\Models\UiSetting::updateOrCreate(
    ['section' => 'professor_sidebar', 'setting_key' => 'hover_color'],
    ['setting_value' => '#8B008B', 'setting_type' => 'color'] // Dark Magenta
);

echo "Professor sidebar colors set to purple theme!<br>";
echo "<a href='/professor/dashboard'>Test Professor Dashboard</a><br>";
echo "<a href='/debug_professor_sidebar.php'>Check Debug Info</a><br>";
echo "<a href='/smartprep/admin/settings'>Admin Settings</a><br>";
