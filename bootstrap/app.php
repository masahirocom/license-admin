<?php

// Bootstrap the application
require_once __DIR__ . '/../vendor/autoload.php';

// Load helper classes
require_once __DIR__ . '/../app/Helpers/UrlHelper.php';

// Load environment variables - check multiple possible locations
$envPaths = [
    __DIR__ . '/..',           // Standard location
    __DIR__ . '/../..',        // If bootstrap is in subdirectory
    '/home/metamondes.com/license-admin'  // Absolute server path
];

$dotenvLoaded = false;
foreach ($envPaths as $path) {
    if (file_exists($path . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable($path);
        $dotenv->load();
        $dotenvLoaded = true;
        break;
    }
}

if (!$dotenvLoaded) {
    throw new Exception('.env file not found in any expected location');
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'pgsql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8',
    'prefix' => '',
    'schema' => 'public',
    'sslmode' => 'prefer',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Helper functions
function config($key, $default = null) {
    $config = require __DIR__ . '/../config/app.php';
    $keys = explode('.', $key);
    $value = $config;
    
    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default;
        }
    }
    
    return $value;
}

function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

function view($template, $data = []) {
    // Try multiple possible view paths
    $viewPaths = [
        __DIR__ . '/../resources/views/',
        __DIR__ . '/../../license-admin/resources/views/',
        '/home/metamondes.com/license-admin/resources/views/'
    ];
    
    $templatePath = null;
    foreach ($viewPaths as $basePath) {
        $testPath = $basePath . str_replace('.', '/', $template) . '.php';
        if (file_exists($testPath)) {
            $templatePath = $testPath;
            break;
        }
    }
    
    if ($templatePath) {
        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
    throw new Exception("View not found: {$template}");
}

function redirect($url) {
    // Add base path if the URL is relative and doesn't already have it
    $basePath = UrlHelper::getBasePath();
    
    // Check if URL starts with / and doesn't already start with the base path
    if ($url[0] === '/' && strpos($url, $basePath . '/') !== 0 && $url !== $basePath) {
        $url = UrlHelper::url(ltrim($url, '/'));
    }
    
    header("Location: {$url}");
    exit;
}

function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

function session($key, $value = null) {
    if ($value === null) {
        return $_SESSION[$key] ?? null;
    }
    $_SESSION[$key] = $value;
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

return $capsule;