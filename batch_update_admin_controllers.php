<?php

require_once 'vendor/autoload.php';

echo "🔧 BATCH UPDATING ADMIN CONTROLLERS\n";
echo "===================================\n\n";

// List of controllers to update
$controllers = [
    'AdminProgramController',
    'AdminModuleController',
    'Admin\BatchEnrollmentController',
    'AdminAnalyticsController', 
    'AdminSettingsController',
    'AdminPackageController',
    'AdminDirectorController',
    'Admin\QuizGeneratorController',
    'Admin\PaymentController'
];

foreach ($controllers as $controller) {
    $filename = str_replace('\\', '/', $controller) . '.php';
    $filepath = "app/Http/Controllers/{$filename}";
    
    echo "Processing: {$controller}\n";
    
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        
        // Check if it already has the trait
        if (strpos($content, 'AdminPreviewCustomization') !== false) {
            echo "  ✅ Already has AdminPreviewCustomization trait\n";
            continue;
        }
        
        // Check if it has previewIndex method
        if (strpos($content, 'previewIndex') === false) {
            echo "  ⚠️ No previewIndex method found\n";
            continue;
        }
        
        // Add the trait import
        $usePattern = '/use ([^;]+);(\s*class\s+\w+\s+extends\s+Controller\s*{)/';
        $useReplacement = 'use $1;
use App\Http\Controllers\Traits\AdminPreviewCustomization;$2
    use AdminPreviewCustomization;';
        
        if (preg_match($usePattern, $content)) {
            $content = preg_replace($usePattern, $useReplacement, $content);
            
            // Update previewIndex method to load customization
            $methodPattern = '/(public function previewIndex\(\$tenant\)\s*{\s*try\s*{\s*)(\/\/ Set preview session)/';
            $methodReplacement = '$1// Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            $2';
            
            $content = preg_replace($methodPattern, $methodReplacement, $content);
            
            // Write back to file
            file_put_contents($filepath, $content);
            echo "  ✅ Updated successfully\n";
        } else {
            echo "  ❌ Could not find pattern to update\n";
        }
    } else {
        echo "  ❌ File not found: {$filepath}\n";
    }
    
    echo "\n";
}

echo "🎉 Batch update completed!\n";
echo "All admin controllers should now have tenant customization support.\n";
