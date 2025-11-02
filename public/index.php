<?php

// Set error reporting for debugging (change to 0 for production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Determine the base path - adjust this based on your server structure
$basePath = '/home/metamondes.com/license-admin';

// Standard structure: public_html/license/ and license-admin/ are siblings
$webPath = __DIR__ . '/../../license-admin/routes/web.php';

if (file_exists($webPath)) {
    require_once $webPath;
} else {
    die("Error: Cannot find routes/web.php at: $webPath<br>Current directory: " . __DIR__);
}