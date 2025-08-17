<?php

namespace App\Http\Controllers\Smartprep\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function notice()
    {
        return view('smartprep.auth.verify');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('smartprep.dashboard');
    }

    public function resend(Request $request)
    {
        if ($request->user()) {
            $request->user()->sendEmailVerificationNotification();
        }
        return back()->with('status', 'Verification link sent!');
    }
}
