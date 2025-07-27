<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeminiQuizService;

class TestAIQuiz extends Command
{
    protected $signature = 'test:ai-quiz';
    protected $description = 'Test AI quiz generation';

    public function handle()
    {
        $this->info('Testing AI Quiz Generation...');
        
        $testText = "
Machine Design is the process of creating and developing machines, mechanisms, and components to meet specific functional requirements. Key concepts include:

1. Stress Analysis: Understanding how materials behave under different types of loads including tension, compression, shear, and torsion.

2. Factor of Safety (FOS): A design factor that provides a margin of safety by ensuring the actual stress is well below the material's ultimate strength.

3. Fatigue Analysis: Study of material failure under repeated or cyclic loading conditions.

4. Material Properties: Understanding mechanical properties like yield strength, ultimate tensile strength, modulus of elasticity, and hardness.

5. Design Optimization: Balancing performance, cost, manufacturability, and reliability in engineering designs.
        ";

        $this->info('Text length: ' . strlen($testText) . ' characters');
        
        try {
            $geminiService = new GeminiQuizService();
            $questions = $geminiService->generateQuizFromText($testText, ['question_count' => 5]);
            
            if ($questions && count($questions) > 0) {
                $this->info('SUCCESS: Generated ' . count($questions) . ' questions');
                
                foreach ($questions as $index => $question) {
                    $this->line('');
                    $this->line('Question ' . ($index + 1) . ':');
                    $this->line('Category: ' . ($question['category'] ?? 'N/A'));
                    $this->line('Type: ' . ($question['type'] ?? 'N/A'));
                    $this->line('Question: ' . ($question['question'] ?? 'N/A'));
                    
                    if (isset($question['options']) && is_array($question['options'])) {
                        $this->line('Options:');
                        foreach ($question['options'] as $key => $value) {
                            $marker = ($question['correct_answer'] === $key) ? ' ✓' : '';
                            $this->line("  $key. $value$marker");
                        }
                    }
                    
                    if (isset($question['explanation'])) {
                        $this->line('Explanation: ' . $question['explanation']);
                    }
                    
                    $this->line(str_repeat('-', 50));
                }
            } else {
                $this->error('ERROR: No questions generated');
            }
            
        } catch (\Exception $e) {
            $this->error('ERROR: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
        }
    }
}
