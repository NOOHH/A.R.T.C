<?php

namespace App\Helpers;

class SessionManager {
    
    /**
     * Check if we're in a Laravel context
     */
    private static function isLaravelContext() {
        return function_exists('app') && app()->bound('session');
    }
    
    public static function init() {
        if (!self::isLaravelContext() && session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        if (self::isLaravelContext()) {
            session([$key => $value]);
        } else {
            self::init();
            $_SESSION[$key] = $value;
        }
    }

    public static function put($key, $value) {
        self::set($key, $value);
    }

    public static function get($key, $default = null) {
        if (self::isLaravelContext()) {
            return session($key, $default);
        } else {
            self::init();
            return $_SESSION[$key] ?? $default;
        }
    }

    public static function has($key) {
        if (self::isLaravelContext()) {
            return session()->has($key);
        } else {
            self::init();
            return isset($_SESSION[$key]);
        }
    }

    public static function remove($key) {
        if (self::isLaravelContext()) {
            session()->forget($key);
        } else {
            self::init();
            unset($_SESSION[$key]);
        }
    }

    public static function getRegistrationData($step) {
        $key = "registration_step{$step}_data";
        return self::get($key);
    }

    public static function destroy() {
        if (self::isLaravelContext()) {
            session()->flush();
            session()->regenerate(true);
        } else {
            self::init();
            session_destroy();
            setcookie('user_id', '', time() - 3600, '/');
            setcookie('user_type', '', time() - 3600, '/');
            setcookie('PHPSESSID', '', time() - 3600, '/');
        }
    }

    public static function isLoggedIn() {
        $userId = self::get('user_id');
        return !empty($userId);
    }

    public static function getUserType() {
        if (self::isLaravelContext()) {
            // Laravel uses 'user_role' instead of 'user_type'
            return session('user_role') ?? session('user_type');
        } else {
            return self::get('user_type');
        }
    }

    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            if (self::isLaravelContext()) {
                return redirect('/')->with('error', 'Please log in to access this page.');
            } else {
                header('Location: /');
                exit();
            }
        }
    }

    public static function requireStudentAuth() {
        if (!self::isLoggedIn() || self::getUserType() !== 'student') {
            if (self::isLaravelContext()) {
                return redirect('/')->with('error', 'Student access required.');
            } else {
                header('Location: /');
                exit();
            }
        }
    }

    public static function requireProfessorAuth() {
        if (!self::isLoggedIn() || self::getUserType() !== 'professor') {
            if (self::isLaravelContext()) {
                return redirect('/')->with('error', 'Professor access required.');
            } else {
                header('Location: /');
                exit();
            }
        }
    }

    public static function requireAdminAuth() {
        if (!self::isLoggedIn() || self::getUserType() !== 'admin') {
            if (self::isLaravelContext()) {
                return redirect('/')->with('error', 'Admin access required.');
            } else {
                header('Location: /');
                exit();
            }
        }
    }
}
