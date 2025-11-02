<?php

/**
 * Base path helper for license admin system
 * Handles proper URL generation when running in subdirectory
 */
class UrlHelper 
{
    private static $basePath = null;
    
    /**
     * Get the base path for the application
     */
    public static function getBasePath(): string 
    {
        if (self::$basePath === null) {
            // Detect if we're running in a subdirectory
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            
            // Extract base path from script name
            // /license/index.php -> /license/
            if (strpos($scriptName, '/license/') !== false) {
                self::$basePath = '/license';
            } elseif (strpos($requestUri, '/license/') !== false) {
                // Try to detect from request URI
                self::$basePath = '/license';
            } else {
                self::$basePath = '';
            }
        }
        
        return self::$basePath;
    }
    
    /**
     * Generate URL with proper base path
     */
    public static function url(string $path = ''): string 
    {
        $basePath = self::getBasePath();
        $path = ltrim($path, '/');
        
        if (empty($path)) {
            return $basePath ?: '/';
        }
        
        return $basePath . '/' . $path;
    }
    
    /**
     * Check if current path matches given path
     */
    public static function isActive(string $path): bool 
    {
        $currentPath = $_SERVER['REQUEST_URI'] ?? '/';
        $basePath = self::getBasePath();
        
        // Remove base path from current path for comparison
        if ($basePath && strpos($currentPath, $basePath) === 0) {
            $currentPath = substr($currentPath, strlen($basePath));
        }
        
        // Remove query string
        $currentPath = explode('?', $currentPath)[0];
        
        return $currentPath === $path || strpos($currentPath, $path) === 0;
    }
}