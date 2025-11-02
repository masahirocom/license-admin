<?php

namespace App\Controllers;

use App\Services\SupabaseService;

class DeviceController
{
    private $supabaseService;
    
    public function __construct()
    {
        $this->supabaseService = new SupabaseService();
    }
    
    public function index()
    {
        try {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'license_key' => $_GET['license_key'] ?? '',
                'is_active' => isset($_GET['is_active']) ? filter_var($_GET['is_active'], FILTER_VALIDATE_BOOLEAN) : null,
            ];
            
            // Remove empty filters
            $filters = array_filter($filters, function($value) {
                return $value !== '' && $value !== null;
            });
            
            $devices = $this->supabaseService->getDevices($filters);
            
            echo view('devices.index', [
                'devices' => $devices,
                'filters' => $_GET
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load devices: ' . $e->getMessage();
            echo view('devices.index', ['devices' => [], 'filters' => []]);
        }
    }
    
    public function remove($deviceId)
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            redirect('/devices');
            return;
        }
        
        try {
            $this->supabaseService->removeDevice($deviceId);
            $_SESSION['success'] = 'Device removed successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to remove device: ' . $e->getMessage();
        }
        
        redirect('/devices');
    }
    
    public function removeFromLicense($licenseKey, $deviceId)
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            redirect("/licenses/show/{$licenseKey}");
            return;
        }
        
        try {
            $this->supabaseService->removeDevice($deviceId);
            $_SESSION['success'] = 'Device removed from license successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to remove device: ' . $e->getMessage();
        }
        
        // Redirect back to license details
        $license = $this->supabaseService->getLicense($licenseKey, true);
        if ($license) {
            redirect("/licenses/show/{$license['id']}");
        } else {
            redirect('/licenses');
        }
    }
    
    private function validateCSRF()
    {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        return $token && $sessionToken && hash_equals($sessionToken, $token);
    }
}