<?php
// Bootstrap Laravel
require_once __DIR__ . '/bootstrap/app.php';

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GeminiQuizService;

// Test the AI quiz generation
$geminiService = new GeminiQuizService();

// Sample text for testing
$testText = "
Machine Design is the process of creating and developing machines, mechanisms, and components to meet specific functional requirements. Key concepts include:

1. Stress Analysis: Understanding how materials behave under different types of loads including tension, compression, shear, and torsion.

2. Factor of Safety (FOS): A design factor that provides a margin of safety by ensuring the actual stress is well below the material's ultimate strength.

3. Fatigue Analysis: Study of material failure under repeated or cyclic loading conditions.

4. Material Properties: Understanding mechanical properties like yield strength, ultimate tensile strength, modulus of elasticity, and hardness.

5. Design Optimization: Balancing performance, cost, manufacturability, and reliability in engineering designs.
";

echo "Testing AI Quiz Generation...\n";
echo "Text length: " . strlen($testText) . " characters\n\n";

try {
    $questions = $geminiService->generateQuizFromText($testText, ['question_count' => 5]);
    
    if ($questions && count($questions) > 0) {
        echo "SUCCESS: Generated " . count($questions) . " questions\n\n";
        
        foreach ($questions as $index => $question) {
            echo "Question " . ($index + 1) . ":\n";
            echo "Category: " . ($question['category'] ?? 'N/A') . "\n";
            echo "Type: " . ($question['type'] ?? 'N/A') . "\n";
            echo "Question: " . ($question['question'] ?? 'N/A') . "\n";
            
            if (isset($question['options']) && is_array($question['options'])) {
                echo "Options:\n";
                foreach ($question['options'] as $key => $value) {
                    $marker = ($question['correct_answer'] === $key) ? " ✓" : "";
                    echo "  $key. $value$marker\n";
                }
            }
            
            if (isset($question['explanation'])) {
                echo "Explanation: " . $question['explanation'] . "\n";
            }
            
            echo "\n" . str_repeat("-", 50) . "\n\n";
        }
    } else {
        echo "ERROR: No questions generated\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
