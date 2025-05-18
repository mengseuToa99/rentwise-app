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
        'room_id',
        'category',
        'description',
        'status',
        'scheduled_date'
    ];
    
    /**
     * Get the tenant that made the maintenance request
     */
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', 'user_id');
    }
    
    /**
     * Get the room associated with the maintenance request
     */
    public function room()
    {
        return $this->belongsTo(RoomDetail::class, 'room_id', 'room_id');
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
        return $query->whereHas('room', function ($q) use ($landlordId) {
            $q->whereHas('property', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            });
        });
    }
} 