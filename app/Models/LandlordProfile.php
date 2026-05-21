<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandlordProfile extends Model
{
    protected $table = 'landlord_profiles';
    protected $primaryKey = 'profile_id';

    protected $fillable = [
        'user_id',
        'business_name',
        'tax_id',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'payout_method',
        'payout_details',
        'notes',
    ];

    protected $casts = [
        'payout_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
