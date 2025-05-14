<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
    public function initials(): string
    {
        return Str::of($this->first_name . ' ' . $this->last_name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
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
