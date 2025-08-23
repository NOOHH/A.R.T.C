<?php

/**
 * Script to fix all admin preview controllers to use mock data instead of database queries
 */

// Controller mapping with their corresponding mock data types
$controllers = [
    'AdminStudentListController' => 'students',
    'AdminProfessorController' => 'professors', 
    'AdminProgramController' => 'programs',
    'AdminModuleController' => 'modules',
    'Admin\\AnnouncementController' => 'announcements',
    'Admin\\BatchEnrollmentController' => 'students',
    'AdminAnalyticsController' => 'analytics',
    'AdminPackageController' => 'packages', // Already updated
    'AdminDirectorController' => 'directors',
    'Admin\\QuizGeneratorController' => 'modules',
    'Admin\\PaymentController' => 'payments'
];

$updated = [];
$errors = [];

foreach ($controllers as $controller => $mockType) {
    $filePath = "app/Http/Controllers/{$controller}.php";
    $absolutePath = "C:\\xampp\\htdocs\\A.R.T.C\\" . str_replace('/', '\\', $filePath);
    
    echo "Processing: {$controller}\n";
    
    if (!file_exists($absolutePath)) {
        $errors[] = "File not found: {$absolutePath}";
        continue;
    }
    
    $content = file_get_contents($absolutePath);
    
    // Skip if already updated (AdminPackageController) 
    if ($controller === 'AdminPackageController') {
        echo "  ✅ Already updated\n";
        continue;
    }
    
    // Look for previewIndex method
    if (strpos($content, 'public function previewIndex') === false) {
        $errors[] = "No previewIndex method found in {$controller}";
        continue;
    }
    
    // Generate appropriate mock data assignment based on controller type
    $mockDataCode = generateMockDataCode($controller, $mockType);
    
    // Update the previewIndex method to use mock data
    $pattern = '/(\$this->loadAdminPreviewCustomization\(\);[\s\S]*?)(return response\(\$html\);)/';
    
    $replacement = '$1' . $mockDataCode . '$2';
    
    $newContent = preg_replace($pattern, $replacement, $content);
    
    if ($newContent && $newContent !== $content) {
        if (file_put_contents($absolutePath, $newContent)) {
            $updated[] = $controller;
            echo "  ✅ Updated successfully\n";
        } else {
            $errors[] = "Failed to write to {$controller}";
        }
    } else {
        $errors[] = "Failed to update {$controller} - pattern not matched or no changes";
    }
}

function generateMockDataCode($controller, $mockType) {
    switch ($controller) {
        case 'AdminStudentListController':
            return '
            
            // Generate mock students data
            $students = $this->generateMockData(\'students\');
            view()->share(\'students\', $students);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        case 'AdminProfessorController':
            return '
            
            // Generate mock professors data
            $professors = $this->generateMockData(\'professors\');
            view()->share(\'professors\', $professors);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        case 'AdminProgramController':
            return '
            
            // Generate mock programs data
            $programs = $this->generateMockData(\'programs\');
            view()->share(\'programs\', $programs);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        case 'AdminModuleController':
            return '
            
            // Generate mock modules data
            $modules = $this->generateMockData(\'modules\');
            view()->share(\'modules\', $modules);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        case 'Admin\\AnnouncementController':
            return '
            
            // Generate mock announcements data 
            $announcements = $this->generateMockData(\'announcements\');
            view()->share(\'announcements\', $announcements);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        case 'Admin\\BatchEnrollmentController':
            return '
            
            // Generate mock data for batch enrollment
            $students = $this->generateMockData(\'students\');
            $programs = $this->generateMockData(\'programs\');
            view()->share(\'students\', $students);
            view()->share(\'programs\', $programs);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        case 'AdminAnalyticsController':
            return '
            
            // Generate mock analytics data
            $analyticsData = $this->generateMockData(\'analytics\');
            view()->share(\'analyticsData\', $analyticsData);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        case 'AdminDirectorController':
            return '
            
            // Generate mock directors data
            $directors = $this->generateMockData(\'directors\');
            view()->share(\'directors\', $directors);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        case 'Admin\\QuizGeneratorController':
            return '
            
            // Generate mock data for quiz generator
            $modules = $this->generateMockData(\'modules\');
            view()->share(\'modules\', $modules);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        case 'Admin\\PaymentController':
            return '
            
            // Generate mock payments data
            $payments = $this->generateMockData(\'payments\');
            view()->share(\'payments\', $payments);
            view()->share(\'isPreviewMode\', true);
            
            ';
            
        default:
            return '
            
            // Generate mock data
            $mockData = $this->generateMockData(\'' . $mockType . '\');
            view()->share(\'mockData\', $mockData);
            view()->share(\'isPreviewMode\', true);
            
            ';
    }
}

echo "\n=== SUMMARY ===\n";
echo "Updated Controllers (" . count($updated) . "):\n";
foreach ($updated as $controller) {
    echo "  ✅ {$controller}\n";
}

if (!empty($errors)) {
    echo "\nErrors (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "  ❌ {$error}\n";
    }
}

echo "\nDone! All admin preview controllers should now use mock data.\n";
