<?php
// Try to actually compile the blade template to catch the exact error
try {
    $bladeCompiler = new \Illuminate\View\Compilers\BladeCompiler(
        new \Illuminate\Filesystem\Filesystem,
        storage_path('framework/views')
    );
    
    $path = resource_path('views/registration/Modular_enrollment.blade.php');
    $contents = file_get_contents($path);
    
    echo "Attempting to compile Blade template...\n";
    
    $compiled = $bladeCompiler->compileString($contents);
    
    echo "Blade compilation successful!\n";
    echo "Now checking PHP syntax of compiled output...\n";
    
    // Write compiled content to temp file and check syntax
    $tempFile = tempnam(sys_get_temp_dir(), 'blade_test');
    file_put_contents($tempFile, $compiled);
    
    exec("php -l $tempFile 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "PHP syntax check passed!\n";
    } else {
        echo "PHP syntax errors found:\n";
        echo implode("\n", $output) . "\n";
    }
    
    unlink($tempFile);
    
} catch (Exception $e) {
    echo "Error during compilation: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}
?>
