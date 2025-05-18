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
        'description'
    ];

    // A utility has many prices (over time)
    public function prices()
    {
        return $this->hasMany(UtilityPrice::class, 'utility_id', 'utility_id');
    }

    // A utility has many usage records
    public function usages()
    {
        return $this->hasMany(UtilityUsage::class, 'utility_id', 'utility_id');
    }
    
    // Get the current price for this utility
    public function getCurrentPrice()
    {
        return $this->prices()
            ->orderBy('effective_date', 'desc')
            ->first();
    }
} 