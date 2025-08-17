<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function index()
    {
        $courses = DB::table('courses')->orderBy('created_at', 'desc')->get();
        return view('tenant.courses.index', compact('courses'));
    }
}



