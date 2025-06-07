<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any maintenance requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->roles->contains(function($role) {
            return in_array(strtolower($role->role_name), ['tenant', 'landlord', 'admin']);
        });
    }

    /**
     * Determine whether the user can view the maintenance request.
     */
    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'admin';
        })) {
            return true;
        }

        if ($user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'tenant';
        })) {
            return $maintenanceRequest->tenant_id === $user->user_id;
        }

        if ($user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        })) {
            return $maintenanceRequest->property->landlord_id === $user->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create maintenance requests.
     */
    public function create(User $user): bool
    {
        return $user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'tenant';
        });
    }

    /**
     * Determine whether the user can update the maintenance request.
     */
    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'admin';
        })) {
            return true;
        }

        if ($user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        })) {
            return $maintenanceRequest->property->landlord_id === $user->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the maintenance request.
     */
    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'admin';
        })) {
            return true;
        }

        if ($user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        })) {
            return $maintenanceRequest->property->landlord_id === $user->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the status of the maintenance request.
     */
    public function updateStatus(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'admin';
        })) {
            return true;
        }

        if ($user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        })) {
            return $maintenanceRequest->property->landlord_id === $user->user_id;
        }

        return false;
    }
} 