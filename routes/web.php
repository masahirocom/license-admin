<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\LicenseController;
use App\Controllers\DeviceController;
use App\Middleware\AuthMiddleware;

// Simple router
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Remove query string
$uri = explode('?', $uri)[0];

// Remove base path if present (for subdirectory deployment)
$basePath = UrlHelper::getBasePath();
if ($basePath && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Ensure URI starts with /
if (empty($uri) || $uri[0] !== '/') {
    $uri = '/' . ltrim($uri, '/');
}

// Route definitions
$routes = [
    // Authentication routes
    'GET /login' => function() {
        AuthMiddleware::guest();
        $controller = new AuthController();
        $controller->showLogin();
    },
    'POST /login' => function() {
        $controller = new AuthController();
        $controller->login();
    },
    'POST /logout' => function() {
        $controller = new AuthController();
        $controller->logout();
    },
    
    // Dashboard routes
    'GET /' => function() {
        AuthMiddleware::handle();
        redirect('/dashboard');
    },
    'GET /dashboard' => function() {
        AuthMiddleware::handle();
        $controller = new DashboardController();
        $controller->index();
    },
    
    // License routes
    'GET /licenses' => function() {
        AuthMiddleware::handle();
        $controller = new LicenseController();
        $controller->index();
    },
    'GET /licenses/create' => function() {
        AuthMiddleware::handle();
        $controller = new LicenseController();
        $controller->create();
    },
    'POST /licenses' => function() {
        AuthMiddleware::handle();
        $controller = new LicenseController();
        $controller->store();
    },
    'GET /licenses/show/(\d+)' => function($id) {
        AuthMiddleware::handle();
        $controller = new LicenseController();
        $controller->show($id);
    },
    'GET /licenses/(\d+)/edit' => function($id) {
        AuthMiddleware::handle();
        $controller = new LicenseController();
        $controller->edit($id);
    },
    'POST /licenses/(\d+)' => function($id) {
        AuthMiddleware::handle();
        $controller = new LicenseController();
        $controller->update($id);
    },
    'POST /licenses/(\d+)/delete' => function($id) {
        AuthMiddleware::handle();
        $controller = new LicenseController();
        $controller->destroy($id);
    },
    'POST /licenses/(\d+)/deactivate' => function($id) {
        AuthMiddleware::handle();
        $controller = new LicenseController();
        $controller->deactivate($id);
    },
    'POST /licenses/(\d+)/activate' => function($id) {
        AuthMiddleware::handle();
        $controller = new LicenseController();
        $controller->activate($id);
    },
    
    // Device routes
    'GET /devices' => function() {
        AuthMiddleware::handle();
        $controller = new DeviceController();
        $controller->index();
    },
    'POST /devices/([^/]+)/remove' => function($deviceId) {
        AuthMiddleware::handle();
        $controller = new DeviceController();
        $controller->remove($deviceId);
    },
    'POST /licenses/([^/]+)/devices/([^/]+)/remove' => function($licenseKey, $deviceId) {
        AuthMiddleware::handle();
        $controller = new DeviceController();
        $controller->removeFromLicense($licenseKey, $deviceId);
    },
];

// Route matching
function matchRoute($routes, $method, $uri) {
    foreach ($routes as $route => $callback) {
        list($routeMethod, $routeUri) = explode(' ', $route, 2);
        
        if ($routeMethod !== $method) {
            continue;
        }
        
        // Convert route pattern to regex
        $pattern = preg_replace('/\([^)]+\)/', '([^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            // Remove the full match
            array_shift($matches);
            
            // Call the callback with captured parameters
            call_user_func_array($callback, $matches);
            return true;
        }
    }
    
    return false;
}

// Try to match the route
if (!matchRoute($routes, $method, $uri)) {
    // 404 Not Found
    http_response_code(404);
    
    // Debug information (remove in production)
    error_log("License Admin: Route not found - Method: $method, URI: $uri, Original URI: " . ($_SERVER['REQUEST_URI'] ?? ''));
    
    echo view('errors.404');
}