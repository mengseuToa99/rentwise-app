<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;
    
    protected $table = 'maintenance_requests';
    protected $primaryKey = 'request_id';
    
    protected $fillable = [
        'tenant_id',
        'property_id',
        'room_id',
        'title',
        'description',
        'priority',
        'status',
        'landlord_notes',
        'completed_at'
    ];
    
    protected $casts = [
        'completed_at' => 'datetime',
    ];
    
    /**
     * Get the tenant that made the maintenance request
     */
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', 'user_id');
    }
    
    /**
     * Get the property associated with the maintenance request
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }
    
    /**
     * Get the room associated with the maintenance request
     */
    public function room()
    {
        return $this->belongsTo(Unit::class, 'room_id', 'room_id');
    }
    
    /**
     * Get the landlord through the property relationship
     */
    public function landlord()
    {
        return $this->property->landlord();
    }
    
    /**
     * Scope a query to only include maintenance requests with a specific status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope a query to only include maintenance requests for a specific tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
    
    /**
     * Scope a query to only include maintenance requests for properties owned by a specific landlord
     */
    public function scopeForLandlord($query, $landlordId)
    {
        return $query->whereHas('property', function ($q) use ($landlordId) {
            $q->where('landlord_id', $landlordId);
        });
    }
} 