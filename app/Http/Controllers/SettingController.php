<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')->orderBy('group')->orderBy('key')->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function save(Request $request)
    {
        $data = $request->input('settings', []);
        foreach ($data as $group => $pairs) {
            foreach ($pairs as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['group' => $group, 'key' => $key],
                    ['value' => $value]
                );
            }
        }
        return redirect()->route('admin.settings.index')->with('status', 'Settings saved');
    }
}



