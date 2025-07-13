<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfessorProgramController extends Controller
{
    /**
     * Get assigned programs for professor
     */
    public function index(Request $request)
    {
        try {
            $professorId = session('user_id');
            $userRole = session('user_role');
            
            if (!$professorId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            if ($userRole !== 'professor') {
                return response()->json(['error' => 'Access denied'], 403);
            }

            // For now, return all programs (you can enhance this later based on your actual relationships)
            $programs = DB::table('programs')
                ->where('is_archived', false)
                ->select('program_id as id', 'program_name as name', 'program_description as description')
                ->orderBy('program_name')
                ->get();

            return response()->json([
                'programs' => $programs
            ]);

        } catch (\Exception $e) {
            Log::error('Professor programs error: ' . $e->getMessage());
            return response()->json([
                'programs' => [],
                'error' => 'Error retrieving assigned programs'
            ], 500);
        }
    }
}
