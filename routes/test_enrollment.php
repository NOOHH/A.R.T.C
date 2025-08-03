<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AdminProgramController;

// Test enrollment management access
Route::get('/test-enrollment-management', function () {
    try {
        $controller = new AdminProgramController();
        $result = $controller->enrollmentManagement();
        
        if ($result instanceof \Illuminate\View\View) {
            return [
                'status' => 'success',
                'view_name' => $result->getName(),
                'data_keys' => array_keys($result->getData()),
                'students_count' => $result->getData()['students']->count(),
                'programs_count' => $result->getData()['programs']->count(),
                'batches_count' => $result->getData()['batches']->count(),
                'courses_count' => $result->getData()['courses']->count(),
            ];
        } else {
            return ['status' => 'error', 'message' => 'Invalid response type'];
        }
    } catch (\Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
});
