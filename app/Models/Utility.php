<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    use HasFactory;

    protected $table = 'utilities';
    protected $primaryKey = 'utility_id';
    public $timestamps = true;

    protected $fillable = [
        'utility_name',
        'unit_of_measure',
        'description',
    ];

    public function prices()
    {
        return $this->hasMany(UtilityPrice::class, 'utility_id', 'utility_id');
    }

    public function usages()
    {
        return $this->hasMany(UtilityUsage::class, 'utility_id', 'utility_id');
    }

    public function meters()
    {
        return $this->hasMany(UtilityMeter::class, 'utility_id', 'utility_id');
    }

    /**
     * Get the current price for this utility, optionally for a specific property.
     * Property-specific prices take precedence over global ones.
     */
    public function getCurrentPrice($propertyId = null)
    {
        $today = now()->toDateString();
        $query = $this->prices()
            ->where('effective_from', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('effective_until')->orWhere('effective_until', '>=', $today);
            });

        if ($propertyId !== null) {
            $propertyPrice = (clone $query)
                ->where('property_id', $propertyId)
                ->orderBy('effective_from', 'desc')
                ->first();
            if ($propertyPrice) {
                return $propertyPrice;
            }
        }

        return $query->whereNull('property_id')
            ->orderBy('effective_from', 'desc')
            ->first();
    }
}
