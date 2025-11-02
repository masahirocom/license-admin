<?php

// Define helper functions first
if (!function_exists('storage_path')) {
    function storage_path($path = '') {
        return __DIR__ . '/../storage/' . ($path ? '/' . $path : '');
    }
}

return [
    'name' => $_ENV['APP_NAME'] ?? 'License Admin Panel',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    'key' => $_ENV['APP_KEY'] ?? '',
    
    'supabase' => [
        'url' => $_ENV['SUPABASE_URL'] ?? '',
        'anon_key' => $_ENV['SUPABASE_ANON_KEY'] ?? '',
        'service_role_key' => $_ENV['SUPABASE_SERVICE_ROLE_KEY'] ?? '',
    ],
    
    'admin' => [
        'email' => $_ENV['ADMIN_EMAIL'] ?? 'admin@example.com',
        'password' => $_ENV['ADMIN_PASSWORD'] ?? 'admin123',
        'session_lifetime' => (int) ($_ENV['ADMIN_SESSION_LIFETIME'] ?? 120),
    ],
    
    'license_api' => [
        'url' => $_ENV['LICENSE_API_URL'] ?? '',
        'key' => $_ENV['LICENSE_API_KEY'] ?? '',
    ],
    
    'session' => [
        'driver' => $_ENV['SESSION_DRIVER'] ?? 'file',
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120),
        'encrypt' => filter_var($_ENV['SESSION_ENCRYPT'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'files' => storage_path('sessions'),
        'connection' => null,
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => 'license_admin_session',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'lax',
    ],
];