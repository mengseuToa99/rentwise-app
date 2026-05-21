<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityMeter extends Model
{
    protected $table = 'utility_meters';
    protected $primaryKey = 'meter_id';

    protected $fillable = [
        'property_id',
        'room_id',
        'utility_id',
        'meter_identifier',
        'allocation_method',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function room()
    {
        return $this->belongsTo(Unit::class, 'room_id', 'room_id');
    }

    public function utility()
    {
        return $this->belongsTo(Utility::class, 'utility_id', 'utility_id');
    }

    public function usages()
    {
        return $this->hasMany(UtilityUsage::class, 'meter_id', 'meter_id');
    }
}
