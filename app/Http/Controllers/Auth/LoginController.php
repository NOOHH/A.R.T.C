<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        $autoEmail = $request->query('auto_email');
        $autoPassword = $request->query('auto_password');
        
        return view('auth.login', compact('autoEmail', 'autoPassword'));
    }

    public function username()
    {
        $identifier = \App\Models\AdminSetting::where('setting_key','login_identifier')->value('setting_value') ?? 'email';
        return $identifier === 'username' ? 'username' : 'email';
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // First, try to authenticate as admin
        if ($this->attemptAdminLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            $this->clearLoginAttempts($request);

            return $this->sendLoginResponse($request);
        }

        // If admin login fails, try regular user login
        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            $this->clearLoginAttempts($request);

            return $this->sendLoginResponse($request);
        }

        // If this was an unsuccessful login attempt, increment the number of attempts
        // to throttle the user into a timeout. When the maximum number of attempts
        // have been exceeded, the user will be locked out for the specified duration.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the admin guard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptAdminLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        // Try to find admin by email
        try {
            $admin = Admin::where('email', $credentials['email'])->first();
            
            if ($admin && Hash::check($credentials['password'], $admin->password)) {
                Auth::guard('admin')->login($admin);
                return true;
            }
        } catch (\Exception $e) {
            // If admin table/connection fails, continue to regular login
        }

        return false;
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    protected function redirectTo()
    {
        // Check if logged in as admin
        if (Auth::guard('admin')->check()) {
            return route('smartprep.admin.dashboard');
        }

        $user = auth()->user();
        
        if ($user && ($user->role === 'admin' || $user->email === 'admin@smartprep.com')) {
            return route('smartprep.admin.dashboard');
        }
        
        return route('dashboard');
    }
}
