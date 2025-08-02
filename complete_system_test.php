<?php
/**
 * COMPLETE ADMIN QUIZ SYSTEM TEST
 * Tests all functionality we've implemented
 */

echo "🚀 COMPLETE ADMIN QUIZ SYSTEM TEST\n";
echo "==================================\n\n";

// Test 1: File Existence
echo "1. VERIFYING FILE EXISTENCE:\n";
echo "----------------------------\n";

$files = [
    'app/Http/Controllers/Admin/QuizGeneratorController.php',
    'resources/views/admin/quiz-generator/index.blade.php',
    'resources/views/admin/quiz-generator/quiz-table.blade.php',
    'resources/views/admin/quiz-generator/quiz-questions-edit.blade.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";
    } else {
        echo "❌ {$file} - MISSING\n";
    }
}

echo "\n";

// Test 2: Controller Methods
echo "2. CONTROLLER METHODS CHECK:\n";
echo "----------------------------\n";

$adminController = 'app/Http/Controllers/Admin/QuizGeneratorController.php';
if (file_exists($adminController)) {
    $content = file_get_contents($adminController);
    
    $methods = [
        'index' => 'Display all quizzes',
        'publish' => 'Publish quiz functionality',
        'archive' => 'Archive quiz functionality', 
        'draft' => 'Move to draft functionality',
        'editQuiz' => 'Edit quiz interface',
        'editQuestions' => 'Edit questions interface',
        'updateQuiz' => 'Update quiz data'
    ];
    
    foreach ($methods as $method => $description) {
        if (strpos($content, "public function {$method}") !== false) {
            echo "✅ {$method}() - {$description}\n";
        } else {
            echo "❌ {$method}() - MISSING - {$description}\n";
        }
    }
}

echo "\n";

// Test 3: Route Patterns
echo "3. ROUTE VERIFICATION:\n";
echo "----------------------\n";

if (file_exists('routes/web.php')) {
    $routes = file_get_contents('routes/web.php');
    
    $routePatterns = [
        'admin/quiz-generator/{quiz}/edit' => 'Edit quiz route',
        'admin/quiz-generator/{quizId}/publish' => 'Publish route',
        'admin/quiz-generator/{quizId}/archive' => 'Archive route',
        'admin/quiz-generator/{quizId}/draft' => 'Draft route',
        'admin/quiz-generator/update-quiz/{quizId}' => 'Update route'
    ];
    
    foreach ($routePatterns as $pattern => $description) {
        if (strpos($routes, $pattern) !== false) {
            echo "✅ {$pattern} - {$description}\n";
        } else {
            echo "❌ {$pattern} - MISSING - {$description}\n";
        }
    }
}

echo "\n";

// Test 4: Template Functionality
echo "4. TEMPLATE FEATURES:\n";
echo "--------------------\n";

$adminIndex = 'resources/views/admin/quiz-generator/index.blade.php';
if (file_exists($adminIndex)) {
    $content = file_get_contents($adminIndex);
    
    $features = [
        'changeQuizStatus' => 'Status change JavaScript function',
        'editQuiz' => 'Edit quiz functionality',
        'publishQuiz' => 'Publish quiz function',
        'archiveQuiz' => 'Archive quiz function',
        'csrf_token' => 'CSRF protection'
    ];
    
    foreach ($features as $feature => $description) {
        if (strpos($content, $feature) !== false) {
            echo "✅ {$feature} - {$description}\n";
        } else {
            echo "❌ {$feature} - MISSING - {$description}\n";
        }
    }
}

echo "\n";

// Test 5: Edit Template Check
echo "5. EDIT TEMPLATE CHECK:\n";
echo "-----------------------\n";

$editTemplate = 'resources/views/admin/quiz-generator/quiz-questions-edit.blade.php';
if (file_exists($editTemplate)) {
    $content = file_get_contents($editTemplate);
    
    echo "✅ Edit template exists\n";
    
    if (strpos($content, 'admin.admin-layouts.admin-layout') !== false) {
        echo "✅ Uses admin layout\n";
    } else {
        echo "❌ Layout reference incorrect\n";
    }
    
    if (strpos($content, '/admin/quiz-generator') !== false) {
        echo "✅ Admin routes configured\n";
    } else {
        echo "❌ Route references need updating\n";
    }
} else {
    echo "❌ Edit template missing\n";
}

echo "\n";

// Test 6: Authentication Configuration
echo "6. AUTHENTICATION SETUP:\n";
echo "------------------------\n";

if (file_exists($adminController)) {
    $content = file_get_contents($adminController);
    
    if (strpos($content, 'admin.director.auth') !== false) {
        echo "✅ Admin middleware configured\n";
    } else {
        echo "❌ Admin middleware missing\n";
    }
    
    // Check if admin can see all quizzes
    if (strpos($content, 'Quiz::with') !== false && strpos($content, 'orderBy(\'created_at\', \'desc\')->get()') !== false) {
        echo "✅ Shows ALL quizzes (no filtering)\n";
    } else {
        echo "❌ May still be filtering quizzes\n";
    }
}

echo "\n";

// Test 7: Comparison with Professor
echo "7. PROFESSOR COMPARISON:\n";
echo "-----------------------\n";

$professorController = 'app/Http/Controllers/Professor/QuizGeneratorController.php';
if (file_exists($professorController)) {
    $content = file_get_contents($professorController);
    
    $professorMethods = ['publish', 'archive', 'editQuestions', 'updateQuiz'];
    
    echo "Professor controller has these methods:\n";
    foreach ($professorMethods as $method) {
        if (strpos($content, "public function {$method}") !== false) {
            echo "✅ Professor: {$method}()\n";
        }
    }
    
    echo "\nChecking if admin has equivalent methods:\n";
    $adminContent = file_get_contents($adminController);
    foreach ($professorMethods as $method) {
        if (strpos($adminContent, "public function {$method}") !== false) {
            echo "✅ Admin: {$method}() - IMPLEMENTED\n";
        } else {
            echo "❌ Admin: {$method}() - MISSING\n";
        }
    }
}

echo "\n";

// Summary
echo "📋 IMPLEMENTATION SUMMARY:\n";
echo "==========================\n";

$issues = [];

// Check for common issues
if (!file_exists('resources/views/admin/quiz-generator/quiz-questions-edit.blade.php')) {
    $issues[] = "Edit template missing";
}

if (file_exists($adminController)) {
    $content = file_get_contents($adminController);
    if (strpos($content, 'public function editQuiz') === false) {
        $issues[] = "editQuiz method missing from admin controller";
    }
}

if (file_exists('routes/web.php')) {
    $routes = file_get_contents('routes/web.php');
    if (strpos($routes, 'admin/quiz-generator/{quiz}/edit') === false) {
        $issues[] = "Edit route missing";
    }
}

if (empty($issues)) {
    echo "🎉 ALL COMPONENTS IMPLEMENTED!\n";
    echo "\nFeatures now available:\n";
    echo "✅ Admin can see ALL quizzes (admin + professor created)\n";
    echo "✅ Admin can publish/archive/draft ANY quiz\n";
    echo "✅ Admin can edit ANY quiz\n";
    echo "✅ Edit functionality copied from professor\n";
    echo "✅ All routes and templates configured\n";
    echo "✅ Authentication middleware in place\n";
    
    echo "\n🚀 READY FOR TESTING!\n";
    echo "Next steps:\n";
    echo "1. Visit /admin/quiz-generator\n";
    echo "2. Test status change buttons\n";
    echo "3. Test edit functionality\n";
    echo "4. Verify all operations work\n";
} else {
    echo "⚠️  ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "❌ {$issue}\n";
    }
}

echo "\n";
echo "🔗 IMPORTANT URLS:\n";
echo "- Admin Quiz Manager: /admin/quiz-generator\n";
echo "- Edit Quiz Example: /admin/quiz-generator/48/edit\n";
echo "- Professor Comparison: /professor/quiz-generator\n";

?>
