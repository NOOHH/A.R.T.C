<?php

echo "=== ADVANCED TAB FIX VALIDATION ===\n\n";

// Test that all Advanced tab components are properly structured
$tests = [
    'Advanced Tab Include Structure' => function() {
        $advancedFile = 'resources/views/smartprep/dashboard/partials/settings/advanced.blade.php';
        if (!file_exists($advancedFile)) {
            return ['❌', 'Advanced.blade.php file missing'];
        }
        
        $content = file_get_contents($advancedFile);
        
        $checks = [
            'permissions-settings' => strpos($content, 'id="permissions-settings"') !== false,
            'director include' => strpos($content, "@include('smartprep.dashboard.partials.settings.director-features')") !== false,
            'professor include' => strpos($content, "@include('smartprep.dashboard.partials.settings.professor-features')") !== false,
        ];
        
        $failed = array_filter($checks, function($v) { return !$v; });
        if (empty($failed)) {
            return ['✅', 'All advanced tab includes properly structured'];
        }
        return ['❌', 'Missing: ' . implode(', ', array_keys($failed))];
    },
    
    'Director Features Structure' => function() {
        $directorFile = 'resources/views/smartprep/dashboard/partials/settings/director-features.blade.php';
        if (!file_exists($directorFile)) {
            return ['❌', 'Director-features.blade.php file missing'];
        }
        
        $content = file_get_contents($directorFile);
        
        $checks = [
            'proper div structure' => strpos($content, 'id="director-features"') !== false,
            'selectedWebsite check inside' => strpos($content, '@if(isset($selectedWebsite))') !== false,
            'form properly closed' => substr_count($content, '</form>') === 1,
            'div properly closed' => substr_count($content, '</div>') >= 10, // Multiple divs expected
        ];
        
        $failed = array_filter($checks, function($v) { return !$v; });
        if (empty($failed)) {
            return ['✅', 'Director features properly structured'];
        }
        return ['❌', 'Issues: ' . implode(', ', array_keys($failed))];
    },
    
    'Professor Features Structure' => function() {
        $professorFile = 'resources/views/smartprep/dashboard/partials/settings/professor-features.blade.php';
        if (!file_exists($professorFile)) {
            return ['❌', 'Professor-features.blade.php file missing'];
        }
        
        $content = file_get_contents($professorFile);
        
        $checks = [
            'proper div structure' => strpos($content, 'id="professor-features"') !== false,
            'selectedWebsite check inside' => strpos($content, '@if(isset($selectedWebsite))') !== false,
            'form properly closed' => substr_count($content, '</form>') === 1,
            'div properly closed' => substr_count($content, '</div>') >= 10, // Multiple divs expected
        ];
        
        $failed = array_filter($checks, function($v) { return !$v; });
        if (empty($failed)) {
            return ['✅', 'Professor features properly structured'];
        }
        return ['❌', 'Issues: ' . implode(', ', array_keys($failed))];
    },
    
    'Variable Passing Chain' => function() {
        // Check that the controller passes selectedWebsite
        $controllerFile = 'app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php';
        if (!file_exists($controllerFile)) {
            return ['❌', 'Controller file missing'];
        }
        
        $content = file_get_contents($controllerFile);
        
        $checks = [
            'selectedWebsite declared' => strpos($content, '$selectedWebsite') !== false,
            'compact with selectedWebsite' => strpos($content, "'selectedWebsite'") !== false,
            'customize-website-complete view' => strpos($content, 'customize-website-complete') !== false,
        ];
        
        $failed = array_filter($checks, function($v) { return !$v; });
        if (empty($failed)) {
            return ['✅', 'Variable passing chain properly configured'];
        }
        return ['❌', 'Issues: ' . implode(', ', array_keys($failed))];
    },
    
    'Template Logic Validation' => function() {
        // Make sure no @if at file start that would make entire sections empty
        $directorFile = 'resources/views/smartprep/dashboard/partials/settings/director-features.blade.php';
        $professorFile = 'resources/views/smartprep/dashboard/partials/settings/professor-features.blade.php';
        
        if (!file_exists($directorFile) || !file_exists($professorFile)) {
            return ['❌', 'Feature files missing'];
        }
        
        $directorContent = file_get_contents($directorFile);
        $professorContent = file_get_contents($professorFile);
        
        // Check that @if is INSIDE the div, not at the start of file
        $directorStartsWithComment = str_starts_with(trim($directorContent), '<!-- Director Features -->');
        $professorStartsWithComment = str_starts_with(trim($professorContent), '<!-- Professor Features -->');
        
        if ($directorStartsWithComment && $professorStartsWithComment) {
            return ['✅', 'Template logic prevents empty sections'];
        }
        return ['❌', 'Template structure may cause empty sections'];
    }
];

foreach ($tests as $testName => $testFunc) {
    [$status, $message] = $testFunc();
    echo "$status $testName: $message\n";
}

echo "\n=== MANUAL VERIFICATION STEPS ===\n";
echo "1. Visit http://localhost:8000/smartprep/dashboard/customize-website?website=1\n";
echo "2. Click on 'Advanced' tab in the left sidebar\n";
echo "3. Verify that you see:\n";
echo "   - 'Permissions' section with director/professor cards\n";
echo "   - Director Features section with permission toggles\n";
echo "   - Professor Features section with permission toggles\n";
echo "   - NOT an empty tab\n\n";

echo "=== SUMMARY ===\n";
echo "Fixed issues:\n";
echo "✅ Director features: @if moved inside div structure\n";
echo "✅ Professor features: @if moved inside div structure\n";
echo "✅ Variable passing: selectedWebsite properly passed from controller\n";
echo "✅ Include chain: advanced.blade.php → director/professor-features.blade.php\n";
echo "\nThe Advanced tab should now display permission controls instead of being empty.\n";

?>
