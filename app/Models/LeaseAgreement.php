<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaseAgreement extends Model
{
    use HasFactory;

    protected $table = 'lease_agreements';
    protected $primaryKey = 'agreement_id';

    protected $fillable = [
        'tenant_id',
        'property_id',
        'room_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'security_deposit',
        'agreement_file_path',
        'status', // active, expired, terminated
        'terms_conditions',
        'signed_by_tenant',
        'signed_by_landlord',
        'signed_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_at' => 'datetime',
        'signed_by_tenant' => 'boolean',
        'signed_by_landlord' => 'boolean',
        'monthly_rent' => 'decimal:2',
        'security_deposit' => 'decimal:2'
    ];

    /**
     * Get the tenant associated with the lease agreement
     */
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', 'user_id');
    }

    /**
     * Get the property associated with the lease agreement
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    /**
     * Get the room/unit associated with the lease agreement
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
     * Check if the lease agreement is currently active
     */
    public function isActive()
    {
        return $this->status === 'active' && 
               now()->between($this->start_date, $this->end_date);
    }

    /**
     * Check if the lease agreement is expired
     */
    public function isExpired()
    {
        return $this->status === 'expired' || 
               ($this->status === 'active' && now()->isAfter($this->end_date));
    }
} 