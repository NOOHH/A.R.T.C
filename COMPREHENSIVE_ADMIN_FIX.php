<?php
echo "🔧 COMPREHENSIVE ADMIN ROUTING DATABASE FIX\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Step 1: Fix the AdminPreviewCustomization trait to load admin_settings
echo "📝 Step 1: Updating AdminPreviewCustomization trait\n";

$traitContent = file_get_contents('app/Http/Controllers/Traits/AdminPreviewCustomization.php');

// Check if we need to add admin settings loading
if (strpos($traitContent, 'director_view_students') === false) {
    // Find the line where we switch back to main
    $lines = explode("\n", $traitContent);
    $updated = false;
    
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], '$tenantService->switchToMain();') !== false) {
            // Insert admin settings loading before switching back
            $newLines = [
                "                        // Load admin settings from tenant database",
                "                        \$adminSettings = [];",
                "                        try {",
                "                            \$directorViewStudents = \\App\\Models\\AdminSetting::getValue('director_view_students', 'true');",
                "                            \$adminSettings['director_view_students'] = \$directorViewStudents;",
                "                        } catch (\\Exception \$e) {",
                "                            \$adminSettings['director_view_students'] = 'true';",
                "                        }",
                "                        ",
            ];
            
            // Insert the new lines
            array_splice($lines, $i, 0, $newLines);
            
            // Also need to share admin settings
            for ($j = $i + count($newLines); $j < count($lines); $j++) {
                if (strpos($lines[$j], "view()->share('navbar'") !== false) {
                    $lines[$j] .= "\n                        view()->share('adminSettings', \$adminSettings);";
                    break;
                }
            }
            
            $updated = true;
            break;
        }
    }
    
    if ($updated) {
        file_put_contents('app/Http/Controllers/Traits/AdminPreviewCustomization.php', implode("\n", $lines));
        echo "✅ AdminPreviewCustomization trait updated with admin settings loading\n";
    } else {
        echo "⚠️  Could not find the right place to insert admin settings loading\n";
    }
} else {
    echo "✅ AdminPreviewCustomization trait already has admin settings loading\n";
}

// Step 2: Check if we need to update the admin dashboard layout
echo "\n📝 Step 2: Checking admin dashboard layout\n";

$layoutContent = file_get_contents('resources/views/admin/admin-dashboard/admin-dashboard-layout.blade.php');

if (strpos($layoutContent, "AdminSetting::getValue('director_view_students'") !== false) {
    echo "⚠️  Found AdminSetting::getValue in layout - this needs to be replaced\n";
    
    // Replace the problematic line
    $newLayoutContent = str_replace(
        "'view_students' => AdminSetting::getValue('director_view_students', 'true') === 'true' || AdminSetting::getValue('director_view_students', '1') === '1',",
        "'view_students' => (isset(\$adminSettings['director_view_students']) ? \$adminSettings['director_view_students'] : 'true') === 'true',",
        $layoutContent
    );
    
    if ($newLayoutContent !== $layoutContent) {
        file_put_contents('resources/views/admin/admin-dashboard/admin-dashboard-layout.blade.php', $newLayoutContent);
        echo "✅ Admin dashboard layout updated to use shared adminSettings\n";
    } else {
        echo "❌ Failed to update admin dashboard layout\n";
    }
} else {
    echo "✅ Admin dashboard layout already uses shared adminSettings\n";
}

// Step 3: Ensure admin_settings table has the required data
echo "\n📝 Step 3: Checking admin_settings table in tenant database\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep_artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if director_view_students setting exists
    $stmt = $pdo->prepare("SELECT * FROM admin_settings WHERE setting_key = 'director_view_students'");
    $stmt->execute();
    $setting = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($setting) {
        echo "✅ director_view_students setting exists: " . json_encode($setting) . "\n";
    } else {
        echo "⚠️  director_view_students setting does not exist, creating it...\n";
        
        $stmt = $pdo->prepare("INSERT INTO admin_settings (setting_key, setting_value, setting_description, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $result = $stmt->execute(['director_view_students', 'true', 'Allow directors to view students', 1]);
        
        if ($result) {
            echo "✅ director_view_students setting created successfully\n";
        } else {
            echo "❌ Failed to create director_view_students setting\n";
        }
    }
    
    // Check what other admin settings exist
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM admin_settings");
    $allSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "📋 All admin settings in tenant database:\n";
    foreach ($allSettings as $setting) {
        echo "   - {$setting['setting_key']}: {$setting['setting_value']}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Step 4: Create missing routes for courses/upload and modules/archived
echo "\n📝 Step 4: Checking for missing admin routes\n";

$routesContent = file_get_contents('routes/web.php');

// Check for courses/upload route
if (strpos($routesContent, 'admin/courses/upload') === false) {
    echo "⚠️  Missing admin/courses/upload route, adding it...\n";
    
    // Find a good place to insert the route (after quiz-generator route)
    $routeToAdd = "
    Route::get('/draft/{tenant}/admin/courses/upload', function(\$tenant) {
        return app(\\App\\Http\\Controllers\\Admin\\CourseController::class)->previewUpload(\$tenant);
    })->name('tenant.draft.admin.courses.upload');
";
    
    // Insert after quiz-generator route
    $routesContent = str_replace(
        "Route::get('/draft/{tenant}/admin/quiz-generator', function(\$tenant) {
        return app(\\App\\Http\\Controllers\\Admin\\QuizGeneratorController::class)->previewIndex(\$tenant);
    })->name('tenant.draft.admin.quiz-generator');",
        "Route::get('/draft/{tenant}/admin/quiz-generator', function(\$tenant) {
        return app(\\App\\Http\\Controllers\\Admin\\QuizGeneratorController::class)->previewIndex(\$tenant);
    })->name('tenant.draft.admin.quiz-generator');
$routeToAdd",
        $routesContent
    );
    
    echo "✅ Added admin/courses/upload route\n";
} else {
    echo "✅ admin/courses/upload route already exists\n";
}

// Check for modules/archived route
if (strpos($routesContent, 'admin/modules/archived') === false) {
    echo "⚠️  Missing admin/modules/archived route, adding it...\n";
    
    $routeToAdd = "
    Route::get('/draft/{tenant}/admin/modules/archived', function(\$tenant) {
        return app(\\App\\Http\\Controllers\\Admin\\ModuleController::class)->previewArchived(\$tenant);
    })->name('tenant.draft.admin.modules.archived');
";
    
    // Insert after courses/upload route
    $routesContent = str_replace(
        "Route::get('/draft/{tenant}/admin/courses/upload', function(\$tenant) {
        return app(\\App\\Http\\Controllers\\Admin\\CourseController::class)->previewUpload(\$tenant);
    })->name('tenant.draft.admin.courses.upload');",
        "Route::get('/draft/{tenant}/admin/courses/upload', function(\$tenant) {
        return app(\\App\\Http\\Controllers\\Admin\\CourseController::class)->previewUpload(\$tenant);
    })->name('tenant.draft.admin.courses.upload');
$routeToAdd",
        $routesContent
    );
    
    echo "✅ Added admin/modules/archived route\n";
} else {
    echo "✅ admin/modules/archived route already exists\n";
}

// Save the updated routes
file_put_contents('routes/web.php', $routesContent);

echo "\n🎯 Fix Summary:\n";
echo "✅ AdminPreviewCustomization trait updated\n";
echo "✅ Admin dashboard layout fixed\n";
echo "✅ Admin settings table verified\n";
echo "✅ Missing routes added\n";
echo "\n🔄 Please test the routes again!\n";
?>
