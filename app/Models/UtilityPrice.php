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
        'price',
        'effective_date'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'effective_date' => 'datetime'
    ];

    // A price belongs to a utility
    public function utility()
    {
        return $this->belongsTo(Utility::class, 'utility_id', 'utility_id');
    }
} 