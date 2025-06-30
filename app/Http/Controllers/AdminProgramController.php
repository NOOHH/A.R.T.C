<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminProgramController extends Controller
{
    /**
     * Display a listing of programs.
     */
    public function index()
    {
        // Load programs with count of students (registrations)
        $programs = Program::withCount('students')->get();

        return view('admin.admin-programs.admin-programs', compact('programs'));
    }

    /**
     * Store a newly created program in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'program_name' => 'required|string|max:100',
        ]);

        Program::create([
            'program_name'         => $request->program_name,
            'created_by_admin_id'  => Auth::user()->admin_id ?? 1, // fallback for demo
        ]);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program added successfully!');
    }

    /**
     * Remove the specified program from storage.
     */
    public function destroy($id)
    {
        $program = Program::findOrFail($id);

        try {
            $program->delete();

            return redirect()
                ->route('admin.programs.index')
                ->with('success', 'Program deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()
                    ->route('admin.programs.index')
                    ->with('error', 'Cannot delete program: it is in use by one or more enrollments.');
            }
            throw $e;
        }
    }

    /**
     * Return JSON list of enrollments for the given program.
     */
    public function enrollments($id)
    {
        $program = Program::findOrFail($id);

        Log::info("Fetching enrollments for program ID: {$id}");

        try {
            // Join students with users on user_id
            $rows = DB::table('students')
                ->leftJoin('users', 'students.user_id', '=', 'users.user_id')
                ->where('students.program_id', $id)
                ->select([
                    'students.firstname',
                    'students.lastname',
                    'students.email',
                    'users.email as user_email',
                    'students.created_at',
                    'students.student_id as id',
                    'students.Start_Date'
                ])
                ->get();

            Log::info('Found ' . $rows->count() . ' student rows');

            $enrollments = $rows->map(function ($r) {
                return [
                    'student_name' => trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? '')) ?: 'Unknown Student',
                    'email'        => $r->email ?: $r->user_email ?: 'No email available',
                    'student_id'   => $r->id,
                    'enrolled_at'  => $r->created_at
                        ? Carbon::parse($r->created_at)->format('M d, Y')
                        : 'Unknown date',
                    'status'       => 'Enrolled',
                    'start_date'   => $r->Start_Date
                        ? Carbon::parse($r->Start_Date)->format('M d, Y')
                        : 'Not set',
                ];
            });

            Log::info('Returning ' . $enrollments->count() . ' enrollment records');

            return response()->json([
                'program_name'      => $program->program_name,
                'total_enrollments' => $enrollments->count(),
                'enrollments'       => $enrollments,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching enrollments: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'error'             => 'Error loading enrollments: ' . $e->getMessage(),
                'total_enrollments' => 0,
                'enrollments'       => [],
            ], 500);
        }
    }
}
