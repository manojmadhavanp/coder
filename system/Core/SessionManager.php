<?php

namespace System\Core;

class SessionManager
{
    private const TOKEN_COOKIE_NAME = 'auth_token';
    private const TOKEN_SESSION_NAME = 'auth_token';
    private const TOKEN_CSRF_NAME = 'csrf_token';

    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getCSRFToken(){
        if (empty($_SESSION[self::TOKEN_CSRF_NAME])) {
            $_SESSION[self::TOKEN_CSRF_NAME] = bin2hex(random_bytes(32));
        }   
        return $_SESSION[self::TOKEN_CSRF_NAME];
    }

    public static function varifyCSRFToken($token){
        return hash_equals($token, $_SESSION[self::TOKEN_CSRF_NAME]);
    }

    public static function createToken()
    {
        $token = bin2hex(random_bytes(32)); // Generate a secure random token
        $_SESSION[self::TOKEN_SESSION_NAME] = $token;
        setcookie(self::TOKEN_COOKIE_NAME, $token, [
            'expires' => time() + 86400, // 1 day expiration
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        return $token;
    }

    public static function validateToken()
    {
        $cookieToken = $_COOKIE[self::TOKEN_COOKIE_NAME] ?? null;
        $sessionToken = $_SESSION[self::TOKEN_SESSION_NAME] ?? null;

        if (!$cookieToken || !$sessionToken) {
            return false;
        }

        return hash_equals($sessionToken, $cookieToken);
    }

    public static function isLoggedIn()
    {
        $isLoggedIn = isset($_SESSION['user_id']);
        Logger::Debug("SessionManager::isLoggedIn() called. Result: " . ($isLoggedIn ? 'true' : 'false'));
        Logger::Debug("Session contents: " . print_r($_SESSION, true));
        return $isLoggedIn;
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
        Logger::Debug("SessionManager::set() called. Key: $key, Value: " . print_r($value, true));
    }

    public static function get($key, $default = null)
    {
        $value = $_SESSION[$key] ?? $default;
        Logger::Debug("SessionManager::get() called. Key: $key, Value: " . print_r($value, true));
        return $value;
    }

    public static function has($key){
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

  

    public static function destroy()
    {
        session_unset();
        session_destroy();
    }
    public static function getUserDetails()
    {
        return $_SESSION;
    }
}
