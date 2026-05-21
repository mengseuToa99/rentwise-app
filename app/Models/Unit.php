<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Unit extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'room_details';
    protected $primaryKey = 'room_id';
    public $timestamps = true;

    protected $fillable = [
        'property_id',
        'pricing_group_id',
        'room_number',
        'type',
        'room_type',
        'floor_number',
        'room_name',
        'description',
        'available',
        'status',
        'rent_amount',
        'due_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'due_date' => 'date:Y-m-d',
        'available' => 'boolean',
        'rent_amount' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function pricingGroup()
    {
        return $this->belongsTo(PricingGroup::class, 'pricing_group_id', 'group_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'room_id', 'room_id');
    }

    public function activeRental()
    {
        return $this->hasOne(Rental::class, 'room_id', 'room_id')->where('status', 'active');
    }

    /**
     * Tenancy history — every rental this room has had, newest first.
     * Use to answer "who used this room over time".
     */
    public function tenancyHistory()
    {
        return $this->hasMany(Rental::class, 'room_id', 'room_id')
            ->with('tenant:user_id,first_name,last_name,username')
            ->orderByDesc('start_date');
    }

    /**
     * Find the rental that was active for this room on a given date.
     * Useful when recording a meter reading for a date in the past.
     */
    public function rentalAt($date): ?Rental
    {
        $d = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : $date;
        return $this->rentals()
            ->where('start_date', '<=', $d)
            ->where(function ($q) use ($d) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $d);
            })
            ->orderByDesc('start_date')
            ->first();
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'room_id', 'room_id');
    }

    public function utilityMeters()
    {
        return $this->hasMany(UtilityMeter::class, 'room_id', 'room_id');
    }

    public function utilityUsages()
    {
        return $this->hasMany(UtilityUsage::class, 'room_id', 'room_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Apply pricing from the associated pricing group
     */
    public function applyGroupPricing()
    {
        if ($this->pricing_group_id && $this->pricingGroup) {
            $this->rent_amount = $this->pricingGroup->base_price;
            $this->type = $this->pricingGroup->room_type;
            $this->room_type = $this->pricingGroup->room_type;
            $this->save();
        }
    }

    // Model Events for Logging
    protected static function booted()
    {
        static::created(function ($unit) {
            $propertyName = $unit->property ? $unit->property->property_name : 'Unknown Property';
            $unit->logCreated('unit', $unit->room_name ?: "Room #{$unit->room_number}", "Added to {$propertyName} - Rent: \${$unit->rent_amount}");
        });

        static::updated(function ($unit) {
            $propertyName = $unit->property ? $unit->property->property_name : 'Unknown Property';
            $unit->logUpdated('unit', $unit->room_name ?: "Room #{$unit->room_number}", "Unit details updated in {$propertyName}");
        });

        static::deleted(function ($unit) {
            $propertyName = $unit->property ? $unit->property->property_name : 'Unknown Property';
            $unit->logDeleted('unit', $unit->room_name ?: "Room #{$unit->room_number}", "Removed from {$propertyName}");
        });
    }
}