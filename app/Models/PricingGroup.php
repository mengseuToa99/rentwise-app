<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingGroup extends Model
{
    use HasFactory;

    protected $table = 'pricing_groups';
    protected $primaryKey = 'group_id';
    public $timestamps = true;
    
    protected $fillable = [
        'property_id',
        'group_name',
        'room_type',
        'description',
        'base_price',
        'amenities',
        'status'
    ];
    
    protected $casts = [
        'amenities' => 'array',
        'base_price' => 'decimal:2'
    ];
    
    /**
     * Get the property this pricing group belongs to
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }
    
    /**
     * Get the units (rooms) that use this pricing group
     */
    public function units()
    {
        return $this->hasMany(Unit::class, 'pricing_group_id', 'group_id');
    }
}
