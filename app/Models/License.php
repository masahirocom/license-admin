<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $table = 'licenses';
    
    protected $fillable = [
        'license_key',
        'email',
        'product_name',
        'version',
        'expires_at',
        'max_devices',
        'is_active',
        'metadata',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array',
        'max_devices' => 'integer'
    ];
    
    public $timestamps = true;
    
    /**
     * Get the devices for the license.
     */
    public function devices()
    {
        return $this->hasMany(Device::class, 'license_key', 'license_key');
    }
    
    /**
     * Check if license is expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
    
    /**
     * Check if license has reached device limit
     */
    public function hasReachedDeviceLimit()
    {
        return $this->devices()->count() >= $this->max_devices;
    }
    
    /**
     * Get remaining device slots
     */
    public function getRemainingDeviceSlots()
    {
        return max(0, $this->max_devices - $this->devices()->count());
    }
    
    /**
     * Scope for active licenses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for expired licenses
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
    
    /**
     * Scope for non-expired licenses
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }
    
    /**
     * Search licenses by email or license key
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('email', 'ILIKE', "%{$search}%")
              ->orWhere('license_key', 'ILIKE', "%{$search}%")
              ->orWhere('product_name', 'ILIKE', "%{$search}%");
        });
    }
    
    /**
     * Filter by product/app
     */
    public function scopeByProduct($query, $product)
    {
        return $query->where('product_name', $product);
    }
    
    /**
     * Filter by version
     */
    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }
}