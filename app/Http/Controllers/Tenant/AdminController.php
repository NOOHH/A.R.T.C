<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard($tenant)
    {
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($tenantModel);
        
        $stats = [
            'total_programs' => DB::table('programs')->count(),
            'total_students' => DB::table('students')->count(),
            'total_professors' => DB::table('professors')->count(),
            'total_enrollments' => DB::table('enrollments')->count(),
        ];
        
        $recentStudents = DB::table('students')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentEnrollments = DB::table('enrollments')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->join('programs', 'enrollments.program_id', '=', 'programs.id')
            ->select('enrollments.*', 'students.first_name', 'students.last_name', 'programs.program_name')
            ->orderBy('enrollments.created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('tenant.artc.admin.dashboard', [
            'tenant' => $tenantModel,
            'stats' => $stats,
            'recentStudents' => $recentStudents,
            'recentEnrollments' => $recentEnrollments,
        ]);
    }
    
    public function programs($tenant)
    {
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($tenantModel);
        
        $programs = DB::table('programs')
            ->leftJoin('directors', 'programs.director_id', '=', 'directors.id')
            ->select('programs.*', 'directors.first_name as director_first_name', 'directors.last_name as director_last_name')
            ->orderBy('programs.created_at', 'desc')
            ->get();
        
        return view('tenant.artc.admin.programs', [
            'tenant' => $tenantModel,
            'programs' => $programs,
        ]);
    }
    
    public function createProgram(Request $request, $tenant)
    {
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($tenantModel);
        
        $request->validate([
            'program_name' => 'required|string|max:255',
            'program_description' => 'nullable|string',
        ]);
        
        DB::table('programs')->insert([
            'program_name' => $request->program_name,
            'program_description' => $request->program_description,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return back()->with('success', 'Program created successfully!');
    }
    
    public function students($tenant)
    {
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($tenantModel);
        
        $students = DB::table('students')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('tenant.artc.admin.students', [
            'tenant' => $tenantModel,
            'students' => $students,
        ]);
    }
    
    public function createStudent(Request $request, $tenant)
    {
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($tenantModel);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
        ]);
        
        // Generate student ID
        $lastStudent = DB::table('students')->orderBy('id', 'desc')->first();
        $nextId = $lastStudent ? $lastStudent->id + 1 : 1;
        $studentId = 'STU' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        
        DB::table('students')->insert([
            'student_id' => $studentId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make('password123'), // Default password
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return back()->with('success', "Student created successfully! Default password: password123");
    }
    
    public function professors($tenant)
    {
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($tenantModel);
        
        $professors = DB::table('professors')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('tenant.artc.admin.professors', [
            'tenant' => $tenantModel,
            'professors' => $professors,
        ]);
    }
    
    public function createProfessor(Request $request, $tenant)
    {
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($tenantModel);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:professors,email',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);
        
        DB::table('professors')->insert([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'department' => $request->department,
            'phone' => $request->phone,
            'password' => Hash::make('password123'), // Default password
            'referral_code' => 'PROF' . rand(1000, 9999),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return back()->with('success', "Professor created successfully! Default password: password123");
    }
    
    public function announcements($tenant)
    {
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($tenantModel);
        
        $announcements = DB::table('announcements')
            ->leftJoin('programs', 'announcements.program_id', '=', 'programs.id')
            ->select('announcements.*', 'programs.program_name')
            ->orderBy('announcements.created_at', 'desc')
            ->get();
            
        $programs = DB::table('programs')->where('is_archived', false)->get();
        
        return view('tenant.artc.admin.announcements', [
            'tenant' => $tenantModel,
            'announcements' => $announcements,
            'programs' => $programs,
        ]);
    }
    
    public function createAnnouncement(Request $request, $tenant)
    {
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $this->switchToTenantDB($tenantModel);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'program_id' => 'nullable|exists:programs,id',
            'is_urgent' => 'boolean',
        ]);
        
        DB::table('announcements')->insert([
            'title' => $request->title,
            'content' => $request->content,
            'program_id' => $request->program_id,
            'author_id' => 1, // Default to first director
            'author_type' => 'director',
            'is_urgent' => $request->has('is_urgent'),
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return back()->with('success', 'Announcement created successfully!');
    }
    
    private function switchToTenantDB($tenantModel)
    {
        $dbName = $tenantModel->database_name;
        if (!$dbName) {
            abort(500, 'Tenant database is not configured.');
        }
        if (config()->has('database.connections.tenant')) {
            config(['database.connections.tenant.database' => $dbName]);
            DB::purge('tenant');
            DB::reconnect('tenant');
        } else {
            config(['database.connections.mysql.database' => $dbName]);
            DB::purge('mysql');
            DB::reconnect('mysql');
        }
    }
}
