<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricingGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'effective_from',
        'effective_until',
        'status',
    ];

    protected $casts = [
        'amenities' => 'array',
        'base_price' => 'decimal:2',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'pricing_group_id', 'group_id');
    }

    public function scopeActive($q)
    {
        $today = now()->toDateString();
        return $q->where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereNull('effective_from')->orWhere('effective_from', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('effective_until')->orWhere('effective_until', '>=', $today);
            });
    }
}
