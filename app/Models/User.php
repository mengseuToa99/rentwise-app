<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Str;

class User extends Authenticatable implements CanResetPasswordContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

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
}
