<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthTestController extends Controller
{
    public function simulateAdminLogin(Request $request)
    {
        // Simulate admin login by setting session variables
        session([
            'user_id' => 1,
            'logged_in' => true,
            'user_type' => 'admin',
            'user_role' => 'admin',
            'name' => 'Test Administrator'
        ]);
        
        // Also set PHP session for compatibility
        $_SESSION['user_id'] = 1;
        $_SESSION['logged_in'] = true;
        $_SESSION['user_type'] = 'admin';
        $_SESSION['user_role'] = 'admin';
        $_SESSION['name'] = 'Test Administrator';
        
        return response()->json([
            'success' => true,
            'message' => 'Admin session simulated successfully',
            'session_data' => session()->all()
        ]);
    }
    
    public function testArchiveWithAuth($id)
    {
        // Test the archive functionality with proper authentication context
        try {
            $controller = new \App\Http\Controllers\AdminModuleController();
            
            // Set up the request context
            request()->headers->set('X-CSRF-TOKEN', csrf_token());
            request()->headers->set('Accept', 'application/json');
            
            $response = $controller->archive($id);
            
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Archive test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
