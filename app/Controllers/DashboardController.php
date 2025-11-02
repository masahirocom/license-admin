<?php

namespace App\Controllers;

use App\Services\SupabaseService;

class DashboardController
{
    private $supabaseService;
    
    public function __construct()
    {
        $this->supabaseService = new SupabaseService();
    }
    
    public function index()
    {
        try {
            $stats = $this->supabaseService->getStatistics();
            
            echo view('dashboard.index', [
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load dashboard: ' . $e->getMessage();
            echo view('dashboard.index', [
                'stats' => [
                    'total_licenses' => 0,
                    'active_licenses' => 0,
                    'expired_licenses' => 0,
                    'total_devices' => 0,
                    'active_devices' => 0,
                ]
            ]);
        }
    }
}