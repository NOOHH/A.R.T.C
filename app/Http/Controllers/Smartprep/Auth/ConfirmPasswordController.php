<?php

namespace App\Http\Controllers\Smartprep\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ConfirmPasswordController extends Controller
{
    public function showConfirmForm()
    {
        return view('smartprep.auth.passwords.confirm');
    }

    public function confirm(Request $request)
    {
        $request->validate(['password' => 'required']);
        if (!Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Password does not match our records.']);
        }
    // Mark password as confirmed for the session
    $request->session()->put('auth.password_confirmed_at', time());
        return redirect()->intended(route('smartprep.dashboard'));
    }
}
