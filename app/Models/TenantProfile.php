<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantProfile extends Model
{
    protected $table = 'tenant_profiles';
    protected $primaryKey = 'profile_id';

    protected $fillable = [
        'user_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'occupation',
        'employer',
        'monthly_income',
        'guarantor_name',
        'guarantor_phone',
        'notes',
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
