<?php

namespace App\Http\Controllers\Smartprep\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Smartprep\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('smartprep.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $login = $request->input('email');
        $password = $request->input('password');

        // Support login by email or username
        $user = User::where('email', $login)
            ->orWhere(function($q) use ($login) {
                $q->where('username', $login)->whereNotNull('username');
            })->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->route('smartprep.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('smartprep.login');
    }
}
