<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function handle()
    {
        // Check if user is authenticated
        if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
            redirect('/login');
            exit;
        }
        
        // Check session timeout
        $sessionLifetime = config('admin.session_lifetime') * 60; // Convert to seconds
        $loginTime = $_SESSION['login_time'] ?? 0;
        
        if (time() - $loginTime > $sessionLifetime) {
            session_destroy();
            session_start();
            $_SESSION['error'] = 'Session expired. Please login again.';
            redirect('/login');
            exit;
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = time();
    }
    
    public static function guest()
    {
        // Redirect authenticated users away from guest pages (like login)
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            redirect('/dashboard');
            exit;
        }
    }
}