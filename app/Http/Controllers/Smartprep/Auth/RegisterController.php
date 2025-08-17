<?php

namespace App\Http\Controllers\Smartprep\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Smartprep\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('smartprep.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'student',
        ]);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        return redirect()->route('smartprep.dashboard');
    }
}
