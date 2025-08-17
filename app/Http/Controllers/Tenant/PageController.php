<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function index()
    {
        $pages = DB::table('pages')->orderBy('created_at', 'desc')->get();
        return view('tenant.admin.pages.index', compact('pages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'slug' => 'required|string',
            'content' => 'nullable|string',
        ]);
        DB::table('pages')->updateOrInsert(['slug' => $data['slug']], [
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return redirect()->back()->with('status', 'Page saved');
    }
}



