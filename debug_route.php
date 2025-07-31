<?php

// Add this to routes/web.php temporarily - for debugging only
Route::post('/debug/quiz-save', function(\Illuminate\Http\Request $request) {
    \Log::info('=== DEBUG: Quiz Save Request ===', [
        'method' => $request->method(),
        'content_type' => $request->header('Content-Type'),
        'has_json' => $request->isJson(),
        'all_input' => $request->all(),
        'json_data' => $request->json()->all(),
        'raw_content' => $request->getContent(),
    ]);
    
    return response()->json([
        'debug' => true,
        'received_data' => $request->all(),
        'json_data' => $request->isJson() ? $request->json()->all() : null,
        'content_type' => $request->header('Content-Type'),
        'method' => $request->method()
    ]);
})->middleware(['web']);
