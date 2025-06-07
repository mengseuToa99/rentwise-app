<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Invoice;

class Rental extends Model
{
    use HasFactory;

    protected $table = 'rental_details';
    protected $primaryKey = 'rental_id';
    public $timestamps = true;

    protected $fillable = [
        'landlord_id',
        'tenant_id',
        'room_id',
        'start_date',
        'end_date',
        'lease_agreement',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id', 'user_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', 'user_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'room_id', 'room_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'rental_id', 'rental_id');
    }

    public function property()
    {
        return $this->hasOneThrough(
            Property::class,
            Unit::class,
            'room_id', // Foreign key on units table
            'property_id', // Foreign key on properties table
            'room_id', // Local key on rentals table
            'property_id' // Local key on units table
        );
    }
} 