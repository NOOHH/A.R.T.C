<?php

namespace App\Helpers;

class SessionManager {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::init();
        $_SESSION[$key] = $value;
    }

    public static function put($key, $value) {
        self::init();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::init();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        self::init();
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        self::init();
        unset($_SESSION[$key]);
    }

    public static function getRegistrationData($step) {
        self::init();
        return $_SESSION["registration_step{$step}_data"] ?? null;
    }

    public static function destroy() {
        self::init();
        session_destroy();
        setcookie('user_id', '', time() - 3600, '/');
        setcookie('user_type', '', time() - 3600, '/');
        setcookie('PHPSESSID', '', time() - 3600, '/');
    }

    public static function isLoggedIn() {
        self::init();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function getUserType() {
        self::init();
        return $_SESSION['user_type'] ?? null;
    }

    public static function requireAuth() {
        self::init();
        if (!self::isLoggedIn()) {
            header('Location: /');
            exit();
        }
    }

    public static function requireStudentAuth() {
        self::init();
        if (!self::isLoggedIn() || self::getUserType() !== 'student') {
            header('Location: /');
            exit();
        }
    }

    public static function requireProfessorAuth() {
        self::init();
        if (!self::isLoggedIn() || self::getUserType() !== 'professor') {
            header('Location: /');
            exit();
        }
    }

    public static function requireAdminAuth() {
        self::init();
        if (!self::isLoggedIn() || self::getUserType() !== 'admin') {
            header('Location: /');
            exit();
        }
    }
}
