<?php
echo "🔍 TESTING CONTROLLER METHOD DIRECTLY\n";
echo "=" . str_repeat("=", 45) . "\n\n";

try {
    // Test the AdminStudentListController previewArchived method directly
    echo "📋 Testing AdminStudentListController::previewArchived...\n";
    
    require_once 'vendor/autoload.php';
    
    // Initialize Laravel
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        \Illuminate\Http\Request::create('/test', 'GET')
    );
    
    // Now test the controller method
    $controller = app(\App\Http\Controllers\AdminStudentListController::class);
    
    if (method_exists($controller, 'previewArchived')) {
        echo "✅ METHOD EXISTS: previewArchived found in AdminStudentListController\n";
        
        try {
            $result = $controller->previewArchived('test1');
            
            if ($result instanceof \Illuminate\View\View) {
                echo "✅ RETURN TYPE: Method returns a View\n";
                echo "📄 VIEW NAME: " . $result->getName() . "\n";
                
                $data = $result->getData();
                echo "📊 VIEW DATA KEYS: " . implode(', ', array_keys($data)) . "\n";
                
                if (isset($data['students'])) {
                    echo "✅ STUDENTS DATA: Found " . count($data['students']) . " students\n";
                } else {
                    echo "❌ STUDENTS DATA: No students data found\n";
                }
                
                if (isset($data['programs'])) {
                    echo "✅ PROGRAMS DATA: Found " . count($data['programs']) . " programs\n";
                } else {
                    echo "❌ PROGRAMS DATA: No programs data found\n";
                }
                
            } else {
                echo "❌ RETURN TYPE: Method does not return a View\n";
                echo "   Actual type: " . gettype($result) . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ METHOD ERROR: " . $e->getMessage() . "\n";
            echo "   File: " . $e->getFile() . "\n";
            echo "   Line: " . $e->getLine() . "\n";
            
            // Print stack trace for debugging
            $trace = $e->getTraceAsString();
            echo "📋 STACK TRACE:\n" . $trace . "\n";
        }
        
    } else {
        echo "❌ METHOD MISSING: previewArchived not found in AdminStudentListController\n";
    }
    
    echo "\n";
    
    // Test AdminProfessorController too
    echo "📋 Testing AdminProfessorController::previewArchived...\n";
    
    $professorController = app(\App\Http\Controllers\AdminProfessorController::class);
    
    if (method_exists($professorController, 'previewArchived')) {
        echo "✅ METHOD EXISTS: previewArchived found in AdminProfessorController\n";
        
        try {
            $result = $professorController->previewArchived('test1');
            
            if ($result instanceof \Illuminate\View\View) {
                echo "✅ RETURN TYPE: Method returns a View\n";
                echo "📄 VIEW NAME: " . $result->getName() . "\n";
                
                $data = $result->getData();
                echo "📊 VIEW DATA KEYS: " . implode(', ', array_keys($data)) . "\n";
                
            } else {
                echo "❌ RETURN TYPE: Method does not return a View\n";
                echo "   Actual type: " . gettype($result) . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ METHOD ERROR: " . $e->getMessage() . "\n";
            echo "   File: " . $e->getFile() . "\n";
            echo "   Line: " . $e->getLine() . "\n";
        }
        
    } else {
        echo "❌ METHOD MISSING: previewArchived not found in AdminProfessorController\n";
    }
    
} catch (Exception $e) {
    echo "❌ SETUP ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

echo "\n💡 ANALYSIS:\n";
echo "=" . str_repeat("-", 30) . "\n";
echo "If methods exist and return Views, the issue might be:\n";
echo "1. Route parameter passing\n";
echo "2. Session or request data conflicts\n";
echo "3. View template issues\n";
echo "4. Middleware interference\n";
?>
