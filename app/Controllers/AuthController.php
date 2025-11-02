<?php

namespace App\Controllers;

class AuthController
{
    public function showLogin()
    {
        // If already authenticated, redirect to dashboard
        if (session('authenticated')) {
            redirect('/dashboard');
            return;
        }
        
        echo view('auth.login');
    }
    
    public function login()
    {
        // CSRF protection
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            redirect('/login');
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate credentials
        if ($this->validateCredentials($email, $password)) {
            $_SESSION['authenticated'] = true;
            $_SESSION['admin_email'] = $email;
            $_SESSION['login_time'] = time();
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            $_SESSION['old'] = ['email' => $email];
            redirect('/login');
        }
    }
    
    public function logout()
    {
        // Destroy session
        session_destroy();
        
        // Start new session for flash messages
        session_start();
        $_SESSION['success'] = 'You have been logged out successfully';
        
        redirect('/login');
    }
    
    private function validateCredentials($email, $password)
    {
        $adminEmail = config('admin.email');
        $adminPassword = config('admin.password');
        
        return $email === $adminEmail && $password === $adminPassword;
    }
    
    private function validateCSRF()
    {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        return $token && $sessionToken && hash_equals($sessionToken, $token);
    }
}