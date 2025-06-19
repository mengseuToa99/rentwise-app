<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Invoice;
use App\Traits\LogsActivity;

class Rental extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'rental_details';
    protected $primaryKey = 'rental_id';
    public $timestamps = true;

    protected $fillable = [
        'landlord_id',
        'tenant_id',
        'room_id',
        'start_date',
        'end_date',
        'lease_agreement',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id', 'user_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', 'user_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'room_id', 'room_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'rental_id', 'rental_id');
    }

    public function property()
    {
        return $this->hasOneThrough(
            Property::class,
            Unit::class,
            'room_id', // Foreign key on units table
            'property_id', // Foreign key on properties table
            'room_id', // Local key on rentals table
            'property_id' // Local key on units table
        );
    }

    // Model Events for Logging
    protected static function booted()
    {
        static::created(function ($rental) {
            $tenantName = $rental->tenant ? $rental->tenant->username : 'Unknown Tenant';
            $unitName = $rental->unit ? ($rental->unit->room_name ?: "Room #{$rental->unit->room_number}") : 'Unknown Unit';
            $rental->logCreated('rental', "Rental #{$rental->rental_id}", "New rental agreement: {$tenantName} -> {$unitName}");
            
            // Update unit status to occupied
            $unit = $rental->unit;
            if ($unit) {
                $unit->available = false;
                $unit->status = 'occupied';
                $unit->save();
            }
        });

        static::updated(function ($rental) {
            $tenantName = $rental->tenant ? $rental->tenant->username : 'Unknown Tenant';
            $unitName = $rental->unit ? ($rental->unit->room_name ?: "Room #{$rental->unit->room_number}") : 'Unknown Unit';
            $rental->logUpdated('rental', "Rental #{$rental->rental_id}", "Rental updated: {$tenantName} -> {$unitName} (Status: {$rental->status})");
            
            // Update unit status based on rental status
            $unit = $rental->unit;
            if ($unit) {
                if (in_array($rental->status, ['expired', 'terminated'])) {
                    $unit->available = true;
                    $unit->status = 'vacant';
                } elseif ($rental->status === 'active') {
                    $unit->available = false;
                    $unit->status = 'occupied';
                }
                $unit->save();
            }
        });

        static::deleted(function ($rental) {
            $tenantName = $rental->tenant ? $rental->tenant->username : 'Unknown Tenant';
            $unitName = $rental->unit ? ($rental->unit->room_name ?: "Room #{$rental->unit->room_number}") : 'Unknown Unit';
            $rental->logDeleted('rental', "Rental #{$rental->rental_id}", "Rental terminated: {$tenantName} -> {$unitName}");
            
            // Update unit status to vacant when rental is deleted
            $unit = $rental->unit;
            if ($unit) {
                $unit->available = true;
                $unit->status = 'vacant';
                $unit->save();
            }
        });
    }
} 