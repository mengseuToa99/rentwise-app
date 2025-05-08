<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomDetail extends Model
{
    use HasFactory;
    
    protected $table = 'room_details';
    protected $primaryKey = 'room_id';
    
    protected $fillable = [
        'property_id',
        'room_name',
        'floor_number',
        'room_number',
        'due_date',
        'room_type',
        'description',
        'available',
        'status',
        'rent_amount'
    ];
    
    /**
     * Get the property this room belongs to
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }
    
    /**
     * Get the current rental for this room
     */
    public function currentRental()
    {
        return $this->hasOne(Rental::class, 'room_id')->where('status', 'active');
    }
    
    /**
     * Get all rentals for this room
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'room_id');
    }
    
    /**
     * Get maintenance requests for this room
     */
    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'room_id');
    }
    
    /**
     * Scope a query to only include vacant rooms
     */
    public function scopeVacant($query)
    {
        return $query->where('status', 'vacant');
    }
    
    /**
     * Scope a query to only include occupied rooms
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }
} 