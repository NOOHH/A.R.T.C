<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\CourseTestController;

/*
|--------------------------------------------------------------------------
| Test API Routes
|--------------------------------------------------------------------------
*/

// Test routes for system verification
Route::group(['prefix' => 'test'], function () {
    
    Route::get('/database', function () {
        try {
            $chatCount = Chat::count();
            $userCount = User::count();
            
            return response()->json([
                'success' => true,
                'chat_count' => $chatCount,
                'user_count' => $userCount,
                'message' => 'Database connection successful'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    });
    
    Route::post('/encryption', function (Request $request) {
        try {
            $originalMessage = $request->input('test_message', 'Default test message');
            
            // Test encryption/decryption
            $encrypted = Crypt::encrypt($originalMessage);
            $decrypted = Crypt::decrypt($encrypted);
            
            return response()->json([
                'success' => true,
                'original' => $originalMessage,
                'encrypted' => $encrypted,
                'decrypted' => $decrypted,
                'match' => $originalMessage === $decrypted
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    });
    
    Route::get('/user-status', function () {
        try {
            $users = User::select('user_id', 'user_firstname', 'user_lastname', 'role', 'is_online')
                         ->take(10)
                         ->get()
                         ->map(function ($user) {
                             return [
                                 'id' => $user->user_id,
                                 'name' => trim($user->user_firstname . ' ' . $user->user_lastname),
                                 'role' => $user->role,
                                 'is_online' => (bool) $user->is_online
                             ];
                         });
            
            $totalUsers = User::count();
            $onlineUsers = User::where('is_online', 1)->count();
            
            return response()->json([
                'success' => true,
                'users' => $users,
                'total_users' => $totalUsers,
                'online_users' => $onlineUsers
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    });

    // Course-level testing routes
    Route::get('/course-access', [CourseTestController::class, 'testCourseAccess']);
    Route::post('/course-enrollment', [CourseTestController::class, 'testCreateCourseEnrollment']);
});
