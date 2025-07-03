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
