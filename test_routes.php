<?php

// Quick diagnostic routes for testing
Route::get('/test-ai-connection', function() {
    try {
        $geminiService = new \App\Services\GeminiQuizService();
        $connection = $geminiService->testConnection();
        
        return response()->json([
            'success' => true,
            'connection' => $connection,
            'message' => $connection ? 'AI service connected successfully' : 'AI service connection failed'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Error testing AI connection'
        ]);
    }
});

Route::post('/test-ai-generate', function() {
    try {
        $geminiService = new \App\Services\GeminiQuizService();
        
        $testText = "Machine Design involves understanding stress analysis, factor of safety, and material properties. Key concepts include tension, compression, shear, and fatigue analysis.";
        
        $questions = $geminiService->generateQuizFromText($testText, ['num_questions' => 2]);
        
        return response()->json([
            'success' => true,
            'questions' => $questions,
            'count' => count($questions),
            'message' => 'AI quiz generation successful'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Error generating AI quiz'
        ]);
    }
});
?>
