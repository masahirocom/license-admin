<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    
    protected $fillable = [
        'device_id',
        'license_key',
        'device_name',
        'device_type',
        'last_seen',
        'is_active',
        'metadata',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'last_seen' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];
    
    public $timestamps = true;
    
    /**
     * Get the license that owns the device.
     */
    public function license()
    {
        return $this->belongsTo(License::class, 'license_key', 'license_key');
    }
    
    /**
     * Check if device is online (last seen within 24 hours)
     */
    public function isOnline()
    {
        if (!$this->last_seen) {
            return false;
        }
        
        return $this->last_seen->diffInHours(now()) <= 24;
    }
    
    /**
     * Scope for active devices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for online devices
     */
    public function scopeOnline($query)
    {
        return $query->where('last_seen', '>', now()->subHours(24));
    }
    
    /**
     * Search devices by device ID or name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('device_id', 'ILIKE', "%{$search}%")
              ->orWhere('device_name', 'ILIKE', "%{$search}%")
              ->orWhere('device_type', 'ILIKE', "%{$search}%");
        });
    }
    
    /**
     * Filter by license key
     */
    public function scopeByLicense($query, $licenseKey)
    {
        return $query->where('license_key', $licenseKey);
    }
}