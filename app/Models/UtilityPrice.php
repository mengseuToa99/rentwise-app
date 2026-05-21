<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityPrice extends Model
{
    use HasFactory;

    protected $table = 'utility_prices';
    protected $primaryKey = 'price_id';
    public $timestamps = true;

    protected $fillable = [
        'utility_id',
        'property_id',
        'price',
        'effective_from',
        'effective_until',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    public function utility()
    {
        return $this->belongsTo(Utility::class, 'utility_id', 'utility_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }
}
