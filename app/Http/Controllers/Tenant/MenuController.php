<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function index()
    {
        $menus = DB::table('menus')->orderBy('created_at', 'desc')->get();
        return view('tenant.admin.menus.index', compact('menus'));
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'items' => 'nullable',
        ]);
        $items = $request->input('items');
        DB::table('menus')->updateOrInsert(['name' => $data['name']], [
            'items' => $items ? json_encode($items) : null,
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return redirect()->back()->with('status', 'Menu saved');
    }
}



