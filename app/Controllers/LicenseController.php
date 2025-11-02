<?php

namespace App\Controllers;

use App\Services\SupabaseService;

class LicenseController
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
                'product' => $_GET['product'] ?? '',
                'version' => $_GET['version'] ?? '',
                'is_active' => isset($_GET['is_active']) ? filter_var($_GET['is_active'], FILTER_VALIDATE_BOOLEAN) : null,
                'expired' => isset($_GET['expired']) ? filter_var($_GET['expired'], FILTER_VALIDATE_BOOLEAN) : null,
            ];
            
            // Remove empty filters
            $filters = array_filter($filters, function($value) {
                return $value !== '' && $value !== null;
            });
            
            $licenses = $this->supabaseService->getLicenses($filters);
            
            echo view('licenses.index', [
                'licenses' => $licenses,
                'filters' => $_GET
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load licenses: ' . $e->getMessage();
            echo view('licenses.index', ['licenses' => [], 'filters' => []]);
        }
    }
    
    public function show($id)
    {
        try {
            $license = $this->supabaseService->getLicense($id);
            
            if (!$license) {
                $_SESSION['error'] = 'License not found';
                redirect('/licenses');
                return;
            }
            
            $devices = $this->supabaseService->getDevicesForLicense($license['license_key']);
            
            echo view('licenses.show', [
                'license' => $license,
                'devices' => $devices
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load license: ' . $e->getMessage();
            redirect('/licenses');
        }
    }
    
    public function create()
    {
        echo view('licenses.create');
    }
    
    public function store()
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            redirect('/licenses/create');
            return;
        }
        
        $data = [
            'license_type' => $_POST['license_type'] ?? 'paid',
            'product_id' => $_POST['product_name'] ?? '',
            'expires_at' => !empty($_POST['expires_at']) ? $_POST['expires_at'] : null,
            'max_devices' => (int) ($_POST['max_devices'] ?? 1),
            'is_active' => !empty($_POST['is_active']),
            'allowed_versions' => !empty($_POST['version']) ? [$_POST['version']] : [],
            'metadata' => ['email' => $_POST['email'] ?? '']
        ];
        
        // Basic validation
        $errors = $this->validateLicenseData($data);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            redirect('/licenses/create');
            return;
        }
        
        // Add metadata if provided
        if (!empty($_POST['metadata'])) {
            try {
                $data['metadata'] = json_decode($_POST['metadata'], true) ?: [];
            } catch (\Exception $e) {
                $data['metadata'] = [];
            }
        }
        
        try {
            $license = $this->supabaseService->createLicense($data);
            $_SESSION['success'] = 'License created successfully. License Key: ' . $license['license_key'];
            redirect('/licenses');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to create license: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            redirect('/licenses/create');
        }
    }
    
    public function edit($id)
    {
        try {
            $license = $this->supabaseService->getLicense($id);
            
            if (!$license) {
                $_SESSION['error'] = 'License not found';
                redirect('/licenses');
                return;
            }
            
            echo view('licenses.edit', ['license' => $license]);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load license: ' . $e->getMessage();
            redirect('/licenses');
        }
    }
    
    public function update($id)
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            redirect("/licenses/{$id}/edit");
            return;
        }
        
        $data = [
            'license_type' => $_POST['license_type'] ?? 'paid',
            'product_id' => $_POST['product_name'] ?? '',
            'expires_at' => !empty($_POST['expires_at']) ? $_POST['expires_at'] : null,
            'max_devices' => (int) ($_POST['max_devices'] ?? 1),
            'is_active' => !empty($_POST['is_active']),
            'allowed_versions' => !empty($_POST['version']) ? [$_POST['version']] : [],
            'metadata' => ['email' => $_POST['email'] ?? '']
        ];
        
        // Basic validation
        $errors = $this->validateLicenseData($data);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            redirect("/licenses/{$id}/edit");
            return;
        }
        
        // Add metadata if provided
        if (!empty($_POST['metadata'])) {
            try {
                $data['metadata'] = json_decode($_POST['metadata'], true) ?: [];
            } catch (\Exception $e) {
                $data['metadata'] = [];
            }
        }
        
        try {
            $this->supabaseService->updateLicense($id, $data);
            $_SESSION['success'] = 'License updated successfully';
            redirect('/licenses');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to update license: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            redirect("/licenses/{$id}/edit");
        }
    }
    
    public function destroy($id)
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            redirect('/licenses');
            return;
        }
        
        try {
            $this->supabaseService->deleteLicense($id);
            $_SESSION['success'] = 'License deleted successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to delete license: ' . $e->getMessage();
        }
        
        redirect('/licenses');
    }
    
    public function deactivate($id)
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            redirect('/licenses');
            return;
        }
        
        try {
            $this->supabaseService->updateLicense($id, ['is_active' => false]);
            $_SESSION['success'] = 'License deactivated successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to deactivate license: ' . $e->getMessage();
        }
        
        redirect('/licenses');
    }
    
    public function activate($id)
    {
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Invalid CSRF token';
            redirect('/licenses');
            return;
        }
        
        try {
            $this->supabaseService->updateLicense($id, ['is_active' => true]);
            $_SESSION['success'] = 'License activated successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to activate license: ' . $e->getMessage();
        }
        
        redirect('/licenses');
    }
    
    private function validateLicenseData($data)
    {
        $errors = [];
        
        // Email is now in metadata
        if (empty($data['metadata']['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['metadata']['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email must be valid';
        }
        
        if (empty($data['license_type'])) {
            $errors['license_type'] = 'License type is required';
        } elseif (!in_array($data['license_type'], ['trial', 'paid'])) {
            $errors['license_type'] = 'License type must be trial or paid';
        }
        
        if (empty($data['product_id'])) {
            $errors['product_name'] = 'Product name is required';
        }
        
        if (empty($data['allowed_versions'])) {
            $errors['version'] = 'Version is required';
        }
        
        if (!empty($data['expires_at'])) {
            $date = strtotime($data['expires_at']);
            if (!$date) {
                $errors['expires_at'] = 'Invalid expiration date';
            } elseif ($date <= time()) {
                $errors['expires_at'] = 'Expiration date must be in the future';
            }
        }
        
        if ($data['max_devices'] < 1) {
            $errors['max_devices'] = 'Max devices must be at least 1';
        }
        
        return $errors;
    }
    
    private function validateCSRF()
    {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        return $token && $sessionToken && hash_equals($sessionToken, $token);
    }
}