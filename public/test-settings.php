<!DOCTYPE html>
<html>
<head>
    <title>Settings Test</title>
</head>
<body>
    <h1>Settings Test Page</h1>
    
    <h2>Current Settings JSON:</h2>
    <?php 
    $settingsPath = __DIR__ . '/../../storage/app/settings.json';
    if (file_exists($settingsPath)) {
        echo '<pre>' . file_get_contents($settingsPath) . '</pre>';
    } else {
        echo '<p>No settings file found. Default settings will be used.</p>';
    }
    ?>
    
    <h2>SettingsHelper Test:</h2>
    <?php
    require_once __DIR__ . '/../../app/Helpers/SettingsHelper.php';
    
    try {
        $settings = App\Helpers\SettingsHelper::getSettings();
        echo '<pre>' . print_r($settings, true) . '</pre>';
        
        echo '<h3>Homepage Styles:</h3>';
        echo '<style>' . App\Helpers\SettingsHelper::getHomepageStyles() . '</style>';
        echo '<pre>' . htmlspecialchars(App\Helpers\SettingsHelper::getHomepageStyles()) . '</pre>';
        
        echo '<h3>Enrollment Styles:</h3>';
        echo '<pre>' . htmlspecialchars(App\Helpers\SettingsHelper::getEnrollmentStyles()) . '</pre>';
        
    } catch (Exception $e) {
        echo '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
    }
    ?>
    
</body>
</html>
