<?php

echo "ADMIN QUIZ SYSTEM DIAGNOSIS\n";
echo "==========================\n\n";

// 1. Check if key files exist
echo "1. FILE EXISTENCE CHECK:\n";
$files = [
    'app/Http/Controllers/Admin/QuizGeneratorController.php',
    'app/Http/Controllers/Professor/QuizGeneratorController.php',
    'resources/views/admin/quiz-generator/index.blade.php',
    'resources/views/admin/quiz-generator/quiz-table.blade.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";
    } else {
        echo "❌ {$file} - MISSING\n";
    }
}

echo "\n2. ADMIN CONTROLLER METHODS:\n";
$adminController = 'app/Http/Controllers/Admin/QuizGeneratorController.php';
if (file_exists($adminController)) {
    $content = file_get_contents($adminController);
    
    $methods = ['publish', 'archive', 'draft', 'editQuiz', 'editQuestions', 'updateQuiz'];
    foreach ($methods as $method) {
        if (strpos($content, "public function {$method}") !== false) {
            echo "✅ {$method}()\n";
        } else {
            echo "❌ {$method}() - MISSING\n";
        }
    }
}

echo "\n3. PROFESSOR CONTROLLER COMPARISON:\n";
$professorController = 'app/Http/Controllers/Professor/QuizGeneratorController.php';
if (file_exists($professorController)) {
    $content = file_get_contents($professorController);
    
    echo "Professor has these edit methods:\n";
    if (strpos($content, 'public function editQuestions') !== false) {
        echo "✅ editQuestions()\n";
    }
    if (strpos($content, 'public function editQuiz') !== false) {
        echo "✅ editQuiz()\n";
    }
    if (strpos($content, 'public function updateQuiz') !== false) {
        echo "✅ updateQuiz()\n";
    }
}

echo "\n4. ROUTE PATTERNS:\n";
if (file_exists('routes/web.php')) {
    $routes = file_get_contents('routes/web.php');
    
    echo "Admin routes:\n";
    if (strpos($routes, 'admin/quiz-generator/{quiz}/edit') !== false) {
        echo "✅ Edit route\n";
    } else {
        echo "❌ Edit route - MISSING\n";
    }
    
    if (strpos($routes, 'admin/quiz-generator/update-quiz/{quiz}') !== false) {
        echo "✅ Update route\n";
    } else {
        echo "❌ Update route - MISSING\n";
    }
    
    echo "\nProfessor routes:\n";
    if (strpos($routes, 'professor/quiz-generator/{quiz}/edit') !== false) {
        echo "✅ Professor edit route exists\n";
    }
}

echo "\n5. TEMPLATE CHECK:\n";
$adminIndex = 'resources/views/admin/quiz-generator/index.blade.php';
if (file_exists($adminIndex)) {
    $content = file_get_contents($adminIndex);
    
    if (strpos($content, 'editQuiz') !== false) {
        echo "✅ Edit button/function exists\n";
    } else {
        echo "❌ Edit button/function - MISSING\n";
    }
}

echo "\n6. JAVASCRIPT FUNCTIONS:\n";
if (file_exists($adminIndex)) {
    $content = file_get_contents($adminIndex);
    
    $jsFunctions = ['changeQuizStatus', 'publishQuiz', 'archiveQuiz', 'editQuiz'];
    foreach ($jsFunctions as $func) {
        if (strpos($content, $func) !== false) {
            echo "✅ {$func}()\n";
        } else {
            echo "❌ {$func}() - MISSING\n";
        }
    }
}

echo "\nISSUES SUMMARY:\n";
echo "==============\n";
echo "Based on this diagnosis, we need to:\n";
echo "1. Add editQuiz/editQuestions methods to admin controller\n";
echo "2. Add edit routes for admin\n";
echo "3. Create edit template for admin\n";
echo "4. Add edit buttons to admin interface\n";
echo "5. Fix authentication issues\n";
echo "6. Test status change functionality\n";

?>
