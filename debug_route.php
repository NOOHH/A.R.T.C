<?php

Route::post('/debug/payment-method', function(\Illuminate\Http\Request $request) {
    \Log::info('Debug payment method request', [
        'all_data' => $request->all(),
        'method_name' => $request->input('method_name'),
        'method_type' => $request->input('method_type'),
        'description' => $request->input('description'),
        'instructions' => $request->input('instructions'),
        'is_enabled' => $request->input('is_enabled'),
        'has_qr_file' => $request->hasFile('qr_code'),
        'qr_file_info' => $request->hasFile('qr_code') ? [
            'name' => $request->file('qr_code')->getClientOriginalName(),
            'size' => $request->file('qr_code')->getSize(),
            'mime_type' => $request->file('qr_code')->getMimeType(),
            'is_valid' => $request->file('qr_code')->isValid()
        ] : null,
        'headers' => $request->headers->all()
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Debug data logged',
        'data' => [
            'method_name' => $request->input('method_name'),
            'method_type' => $request->input('method_type'),
            'has_file' => $request->hasFile('qr_code')
        ]
    ]);
});
