<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Property extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'property_details';
    protected $primaryKey = 'property_id';
    public $timestamps = true;

    protected $fillable = [
        'landlord_id',
        'property_name',
        'house_building_number',
        'building_number',
        'street_number',
        'street',
        'village',
        'commune',
        'district',
        'province',
        'total_floors',
        'total_rooms',
        'description',
        'status',
        'property_type',
        'year_built',
        'property_size',
        'size_measurement',
        'amenities',
        'is_pets_allowed',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_pets_allowed' => 'boolean',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id', 'user_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'property_id', 'property_id');
    }

    public function rentals()
    {
        // Rentals don't have a direct property_id anymore — they go through units
        return $this->hasManyThrough(
            Rental::class,
            Unit::class,
            'property_id', // FK on units
            'room_id',     // FK on rentals
            'property_id', // local key on properties
            'room_id'      // local key on units
        );
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'property_id', 'property_id');
    }

    public function pricingGroups()
    {
        return $this->hasMany(PricingGroup::class, 'property_id', 'property_id');
    }

    public function utilityMeters()
    {
        return $this->hasMany(UtilityMeter::class, 'property_id', 'property_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /** Backwards-compat alias for old code that called $property->propertyImages */
    public function propertyImages()
    {
        return $this->images();
    }

    public function __toString()
    {
        return $this->property_name;
    }

    protected static function booted()
    {
        static::created(function ($property) {
            $property->logCreated('property', $property->property_name, "Address: {$property->street}, {$property->village}");
        });

        static::updated(function ($property) {
            $property->logUpdated('property', $property->property_name, "Property details updated");
        });

        static::deleted(function ($property) {
            $property->logDeleted('property', $property->property_name, "Property removed from system");
        });
    }
}
