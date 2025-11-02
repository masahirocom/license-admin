<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SupabaseService
{
    private $client;
    private $baseUrl;
    private $apiKey;
    
    public function __construct()
    {
        $this->baseUrl = $_ENV['SUPABASE_URL'];
        $this->apiKey = $_ENV['SUPABASE_SERVICE_ROLE_KEY'];
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'apikey' => $this->apiKey,
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ]
        ]);
    }
    
    /**
     * Get all licenses with optional filtering
     */
    public function getLicenses($filters = [])
    {
        try {
            $query = [];
            
            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query['or'] = "(license_key.ilike.*{$search}*,license_type.ilike.*{$search}*,product_id.ilike.*{$search}*)";
            }
            
            if (isset($filters['product'])) {
                $query['license_type'] = "eq.{$filters['product']}";
            }
            
            if (isset($filters['version'])) {
                $query['version'] = "eq.{$filters['version']}";
            }
            
            if (isset($filters['is_active'])) {
                $query['is_active'] = $filters['is_active'] ? 'eq.true' : 'eq.false';
            }
            
            if (isset($filters['expired'])) {
                if ($filters['expired']) {
                    $query['expires_at'] = 'lt.' . date('c');
                } else {
                    $query['expires_at'] = 'gt.' . date('c');
                }
            }
            
            $queryString = http_build_query($query);
            $response = $this->client->get("/rest/v1/licenses?{$queryString}&order=created_at.desc");
            
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \Exception('Failed to fetch licenses: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a single license by ID or license key
     */
    public function getLicense($identifier, $byKey = false)
    {
        try {
            $field = $byKey ? 'license_key' : 'id';
            $response = $this->client->get("/rest/v1/licenses?{$field}=eq.{$identifier}");
            $licenses = json_decode($response->getBody(), true);
            
            return !empty($licenses) ? $licenses[0] : null;
        } catch (RequestException $e) {
            throw new \Exception('Failed to fetch license: ' . $e->getMessage());
        }
    }
    
    /**
     * Create a new license
     */
    public function createLicense($data)
    {
        try {
            $licenseData = [
                'license_key' => $this->generateLicenseKey(),
                'license_type' => $data['license_type'],
                'product_id' => $data['product_id'],
                'expires_at' => $data['expires_at'],
                'max_devices' => (int) $data['max_devices'],
                'is_active' => $data['is_active'] ?? true,
                'allowed_versions' => $data['allowed_versions'] ?? [],
                'metadata' => $data['metadata'] ?? [],
                'created_at' => date('c')
            ];
            
            // Debug log
            error_log("Creating license with data: " . json_encode($licenseData, JSON_PRETTY_PRINT));
            
            $response = $this->client->post('/rest/v1/licenses', [
                'json' => $licenseData
            ]);
            
            $result = json_decode($response->getBody(), true);
            return !empty($result) ? $result[0] : $licenseData;
        } catch (RequestException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response';
            error_log("Supabase error: " . $errorBody);
            throw new \Exception('Failed to create license: ' . $e->getMessage());
        }
    }
    
    /**
     * Update a license
     */
    public function updateLicense($id, $data)
    {
        try {
            $updateData = array_filter([
                'license_type' => $data['license_type'] ?? null,
                'product_id' => $data['product_id'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'max_devices' => isset($data['max_devices']) ? (int) $data['max_devices'] : null,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : null,
                'allowed_versions' => $data['allowed_versions'] ?? null,
                'metadata' => $data['metadata'] ?? null
            ], function($value) {
                return $value !== null;
            });
            
            $response = $this->client->patch("/rest/v1/licenses?id=eq.{$id}", [
                'json' => $updateData
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \Exception('Failed to update license: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a license
     */
    public function deleteLicense($id)
    {
        try {
            $this->client->delete("/rest/v1/licenses?id=eq.{$id}");
            return true;
        } catch (RequestException $e) {
            throw new \Exception('Failed to delete license: ' . $e->getMessage());
        }
    }
    
    /**
     * Get devices for a license
     */
    public function getDevicesForLicense($licenseKey)
    {
        try {
            // created_at may not exist, use id for ordering or no ordering
            $response = $this->client->get("/rest/v1/devices?license_key=eq.{$licenseKey}");
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \Exception('Failed to fetch devices: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all devices with optional filtering
     */
    public function getDevices($filters = [])
    {
        try {
            $query = [];
            
            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query['or'] = "(device_id.ilike.*{$search}*,device_name.ilike.*{$search}*,device_type.ilike.*{$search}*)";
            }
            
            if (isset($filters['license_key'])) {
                $query['license_key'] = "eq.{$filters['license_key']}";
            }
            
            if (isset($filters['is_active'])) {
                $query['is_active'] = $filters['is_active'] ? 'eq.true' : 'eq.false';
            }
            
            $queryString = http_build_query($query);
            // created_at may not exist, remove ordering or use existing column
            $response = $this->client->get("/rest/v1/devices?{$queryString}");
            
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \Exception('Failed to fetch devices: ' . $e->getMessage());
        }
    }
    
    /**
     * Deactivate/Remove a device
     */
    public function removeDevice($deviceId)
    {
        try {
            $this->client->delete("/rest/v1/devices?device_id=eq.{$deviceId}");
            return true;
        } catch (RequestException $e) {
            throw new \Exception('Failed to remove device: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate a unique license key
     */
    private function generateLicenseKey()
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8) . '-' . 
                         substr(md5(uniqid(mt_rand(), true)), 0, 8) . '-' . 
                         substr(md5(uniqid(mt_rand(), true)), 0, 8) . '-' . 
                         substr(md5(uniqid(mt_rand(), true)), 0, 8));
    }
    
    /**
     * Get statistics
     */
    public function getStatistics()
    {
        try {
            // Get license counts
            $allLicenses = $this->client->get('/rest/v1/licenses?select=id,is_active,expires_at');
            $licenses = json_decode($allLicenses->getBody(), true);
            
            $totalLicenses = count($licenses);
            $activeLicenses = count(array_filter($licenses, function($l) { return $l['is_active']; }));
            $expiredLicenses = count(array_filter($licenses, function($l) { 
                return $l['expires_at'] && strtotime($l['expires_at']) < time(); 
            }));
            
            // Get device count
            $allDevices = $this->client->get('/rest/v1/devices?select=id,is_active');
            $devices = json_decode($allDevices->getBody(), true);
            
            $totalDevices = count($devices);
            $activeDevices = count(array_filter($devices, function($d) { return $d['is_active']; }));
            
            return [
                'total_licenses' => $totalLicenses,
                'active_licenses' => $activeLicenses,
                'expired_licenses' => $expiredLicenses,
                'total_devices' => $totalDevices,
                'active_devices' => $activeDevices,
            ];
        } catch (RequestException $e) {
            throw new \Exception('Failed to fetch statistics: ' . $e->getMessage());
        }
    }
}