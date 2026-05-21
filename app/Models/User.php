<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Str;
use App\Traits\LogsActivity;

class User extends Authenticatable implements CanResetPasswordContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword, LogsActivity, SoftDeletes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password_hash',
        'email',
        'phone_number',
        'profile_picture',
        'id_card_picture',
        'status',
        'last_login',
        'failed_login_attempts',
        'first_name',
        'last_name',
        'google_id',
        'facebook_id',
        'telegram_id',
        'phone',
        'avatar',
        'provider',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_login' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials()
    {
        $name = $this->name;
        $words = explode(' ', $name);
        
        if (count($words) >= 2) {
            return mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1);
        }
        
        return mb_substr($name, 0, 2);
    }

    /**
     * Get the name attribute (for backward compatibility)
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the password for the user (for Laravel authentication)
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
    
    /**
     * Get the password attribute for Laravel's Password Broker
     * This is needed for password reset to work properly
     */
    public function getPasswordAttribute()
    {
        return $this->password_hash;
    }
    
    /**
     * Set the password attribute for Laravel's Password Broker
     * This is needed for password reset to work properly
     */
    public function setPasswordAttribute($value)
    {
        if ($value && !str_starts_with($value, '$2y$')) {
            // Only hash if it's not already hashed
            $value = bcrypt($value);
        }
        $this->attributes['password_hash'] = $value;
    }
    
    /**
     * Get the user's roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }
    
    /**
     * Get the user's verification
     */
    public function verification()
    {
        return $this->hasOne(UserVerification::class, 'user_id');
    }

    /**
     * Get the user's sessions
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }

    /**
     * Role-specific profile (only one of these will exist per user).
     */
    public function landlordProfile()
    {
        return $this->hasOne(LandlordProfile::class, 'user_id', 'user_id');
    }

    public function tenantProfile()
    {
        return $this->hasOne(TenantProfile::class, 'user_id', 'user_id');
    }

    /** Properties owned (landlord) */
    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id', 'user_id');
    }

    /** Rentals as tenant */
    public function tenantRentals()
    {
        return $this->hasMany(Rental::class, 'tenant_id', 'user_id');
    }

    /** Rentals as landlord */
    public function landlordRentals()
    {
        return $this->hasMany(Rental::class, 'landlord_id', 'user_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Every utility reading attributed to this user as a tenant —
     * across all their rentals, past and present.
     */
    public function utilityUsages()
    {
        return $this->hasManyThrough(
            UtilityUsage::class,
            Rental::class,
            'tenant_id',  // FK on rentals
            'rental_id',  // FK on utility_usages
            'user_id',    // local key on users
            'rental_id'   // local key on rentals
        );
    }

    /**
     * Per-utility totals for this tenant across all their rentals.
     * Returns: [['utility_id'=>1,'utility_name'=>'Electricity','total'=>1234.5], ...]
     */
    public function utilityConsumptionByType(): array
    {
        $rentalIds = $this->tenantRentals()->pluck('rental_id');
        if ($rentalIds->isEmpty()) {
            return [];
        }
        return UtilityUsage::whereIn('rental_id', $rentalIds)
            ->selectRaw('utility_id, SUM(amount_used) as total')
            ->groupBy('utility_id')
            ->with('utility:utility_id,utility_name,unit_of_measure')
            ->get()
            ->map(fn ($row) => [
                'utility_id' => $row->utility_id,
                'utility_name' => $row->utility?->utility_name,
                'unit_of_measure' => $row->utility?->unit_of_measure,
                'total' => (float) $row->total,
            ])
            ->toArray();
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($permission)
    {
        // Check if user has any roles
        if (!$this->roles) {
            return false;
        }
        
        // Get all role IDs for this user
        $roleIds = $this->roles->pluck('role_id')->toArray();
        
        // Check if any of the user's roles have this permission
        $hasPermission = AccessPermission::whereIn('role_id', $roleIds)
            ->where('permission_name', $permission)
            ->exists();
        
        return $hasPermission;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        // Check if user has any roles
        if (!$this->roles) {
            return false;
        }
        
        // Check if the user has the role by name
        return $this->roles->contains('role_name', $roleName);
    }

    /**
     * Get all permissions for this user (through all their roles)
     */
    public function getAllPermissions()
    {
        // Get all role IDs for this user
        $roleIds = $this->roles->pluck('role_id')->toArray();
        
        // Get all permissions for these roles
        return AccessPermission::whereIn('role_id', $roleIds)
            ->with('permissionGroup')
            ->get();
    }

    // Model Events for Logging
    protected static function booted()
    {
        static::created(function ($user) {
            $user->logCreated('user', $user->username, "Email: {$user->email}");
        });

        static::updated(function ($user) {
            $changes = [];
            if ($user->isDirty('status')) {
                $changes[] = "Status changed to: {$user->status}";
            }
            if ($user->isDirty('email')) {
                $changes[] = "Email updated";
            }
            if ($user->isDirty('phone_number')) {
                $changes[] = "Phone updated";
            }
            
            $changeDescription = !empty($changes) ? implode(', ', $changes) : "Profile updated";
            $user->logUpdated('user', $user->username, $changeDescription);
        });

        static::deleted(function ($user) {
            $user->logDeleted('user', $user->username, "User account removed");
        });
    }
}
