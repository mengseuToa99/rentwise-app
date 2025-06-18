<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Unit extends Model
{
    use HasFactory;
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