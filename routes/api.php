<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Programs list API route for navbar dropdown
Route::get('/programs', function () {
    $programs = \App\Models\Program::where('is_archived', false)
        ->select('program_id', 'program_name', 'program_description')
        ->orderBy('program_name')
        ->get();
    return response()->json(['success' => true, 'data' => $programs]);
});

// Program details API route
Route::get('/programs/{id}', function ($id) {
    $program = \App\Models\Program::find($id);
    if (!$program || $program->is_archived) {
        return response()->json(['error' => 'Program not found'], 404);
    }
    return response()->json($program);
});

// Program modules API route
Route::get('/programs/{id}/modules', function ($id) {
    $modules = \App\Models\Module::where('program_id', $id)
        ->where('is_archived', false)
        ->get();
    return response()->json($modules);
}); 
