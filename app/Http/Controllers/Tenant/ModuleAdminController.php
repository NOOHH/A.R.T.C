<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ModuleAdminController extends Controller
{
    public function index()
    {
        $courses = DB::table('courses')->get();
        $modules = DB::table('modules')->join('courses','modules.course_id','=','courses.id')
            ->select('modules.*','courses.title as course_title')->orderBy('modules.created_at','desc')->get();
        return view('tenant.admin.modules.index', compact('courses','modules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|integer',
            'title' => 'required|string',
            'content' => 'nullable|string',
        ]);
        DB::table('modules')->insert([
            'course_id' => $data['course_id'],
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->back()->with('status', 'Module created');
    }
}



