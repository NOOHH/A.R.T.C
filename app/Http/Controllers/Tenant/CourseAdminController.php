<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseAdminController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);
        DB::table('courses')->insert([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->back()->with('status', 'Course created');
    }
}



