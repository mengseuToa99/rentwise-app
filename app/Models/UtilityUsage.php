<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityUsage extends Model
{
    use HasFactory;

    protected $table = 'utility_usages';
    protected $primaryKey = 'usage_id';
    public $timestamps = true;

    protected $fillable = [
        'room_id',
        'utility_id',
        'usage_date',
        'old_meter_reading',
        'new_meter_reading',
        'amount_used'
    ];

    protected $casts = [
        'usage_date' => 'datetime',
        'old_meter_reading' => 'decimal:2',
        'new_meter_reading' => 'decimal:2',
        'amount_used' => 'decimal:2'
    ];

    // A usage record belongs to a utility
    public function utility()
    {
        return $this->belongsTo(Utility::class, 'utility_id', 'utility_id');
    }

    // A usage record belongs to a room/unit
    public function room()
    {
        return $this->belongsTo(Unit::class, 'room_id', 'room_id');
    }
    
    // Calculate the amount used
    public function calculateUsage()
    {
        return $this->new_meter_reading - $this->old_meter_reading;
    }
    
    // Calculate the charge for this usage based on current utility price
    public function calculateCharge()
    {
        $price = $this->utility->getCurrentPrice();
        if (!$price) {
            return 0;
        }
        
        return $this->amount_used * $price->price;
    }
} 