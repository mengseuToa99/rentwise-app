<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;
use App\Models\AccessPermission;
use App\Models\PermissionGroup;

class PermissionManagement extends Component
{
    use WithPagination;
    
    public $selectedRole = null;
    public $selectedRoleId = null;
    public $selectedGroupId = null;
    public $searchPermission = '';
    public $permissionGroups = [];
    public $rolePermissions = [];
    public $allPermissions = [];
    
    // Form fields for creating a new permission
    public $newPermissionName = '';
    public $newPermissionDescription = '';
    public $newPermissionGroupId = null;
    
    public function mount()
    {
        $this->loadPermissionGroups();
        
        // Auto-select the first role if available
        $firstRole = Role::first();
        if ($firstRole) {
            $this->selectRole($firstRole->role_id);
        }
    }
    
    public function loadPermissionGroups()
    {
        // Check if permission groups exist, if not create default ones
        $groupCount = PermissionGroup::count();
        
        if ($groupCount === 0) {
            // Create default permission groups
            $defaultGroups = [
                ['group_name' => 'User Management', 'description' => 'Permissions related to user account management'],
                ['group_name' => 'Property Management', 'description' => 'Permissions related to property and unit management'],
                ['group_name' => 'Rental Management', 'description' => 'Permissions related to rental agreements'],
                ['group_name' => 'Financial Management', 'description' => 'Permissions related to invoices and payments'],
                ['group_name' => 'System Administration', 'description' => 'Permissions related to system settings and configuration']
            ];
            
            foreach ($defaultGroups as $group) {
                PermissionGroup::create($group);
            }
        }
        
        $this->permissionGroups = PermissionGroup::all();
        
        // If we have no permissions at all, create some default ones
        $permissionCount = AccessPermission::count();
        
        if ($permissionCount === 0 && $this->selectedRoleId) {
            $adminRoleId = Role::where('role_name', 'admin')->value('role_id');
            
            if ($adminRoleId) {
                // Create some basic default permissions for admin
                $userManagementGroup = PermissionGroup::where('group_name', 'User Management')->first();
                $propertyManagementGroup = PermissionGroup::where('group_name', 'Property Management')->first();
                $systemAdminGroup = PermissionGroup::where('group_name', 'System Administration')->first();
                
                if ($userManagementGroup) {
                    $permissions = [
                        ['permission_name' => 'create_user', 'description' => 'Create new users'],
                        ['permission_name' => 'edit_user', 'description' => 'Edit existing users'],
                        ['permission_name' => 'delete_user', 'description' => 'Delete users'],
                        ['permission_name' => 'view_user', 'description' => 'View user details']
                    ];
                    
                    foreach ($permissions as $perm) {
                        AccessPermission::create([
                            'role_id' => $adminRoleId,
                            'permission_name' => $perm['permission_name'],
                            'description' => $perm['description'],
                            'group_id' => $userManagementGroup->group_id
                        ]);
                    }
                }
                
                if ($propertyManagementGroup) {
                    $permissions = [
                        ['permission_name' => 'create_property', 'description' => 'Create new properties'],
                        ['permission_name' => 'edit_property', 'description' => 'Edit existing properties'],
                        ['permission_name' => 'delete_property', 'description' => 'Delete properties'],
                        ['permission_name' => 'view_property', 'description' => 'View property details']
                    ];
                    
                    foreach ($permissions as $perm) {
                        AccessPermission::create([
                            'role_id' => $adminRoleId,
                            'permission_name' => $perm['permission_name'],
                            'description' => $perm['description'],
                            'group_id' => $propertyManagementGroup->group_id
                        ]);
                    }
                }
                
                if ($systemAdminGroup) {
                    $permissions = [
                        ['permission_name' => 'manage_roles', 'description' => 'Manage user roles'],
                        ['permission_name' => 'manage_permissions', 'description' => 'Manage permissions'],
                        ['permission_name' => 'system_settings', 'description' => 'Change system settings']
                    ];
                    
                    foreach ($permissions as $perm) {
                        AccessPermission::create([
                            'role_id' => $adminRoleId,
                            'permission_name' => $perm['permission_name'],
                            'description' => $perm['description'],
                            'group_id' => $systemAdminGroup->group_id
                        ]);
                    }
                }
            }
        }
    }
    
    public function selectRole($roleId)
    {
        $this->selectedRoleId = $roleId;
        $this->selectedRole = Role::find($roleId);
        $this->loadRolePermissions();
    }
    
    public function selectGroup($groupId = null)
    {
        $this->selectedGroupId = $groupId;
        $this->resetPage();
    }
    
    public function loadRolePermissions()
    {
        if (!$this->selectedRoleId) {
            return;
        }
        
        // Get current permissions
        $this->rolePermissions = AccessPermission::where('role_id', $this->selectedRoleId)
            ->pluck('permission_name')
            ->toArray();
    }
    
    public function togglePermission($permissionName)
    {
        if (!$this->selectedRoleId) {
            return;
        }
        
        $permissionExists = AccessPermission::where('role_id', $this->selectedRoleId)
            ->where('permission_name', $permissionName)
            ->first();
            
        if ($permissionExists) {
            // Remove permission
            $permissionExists->delete();
            $this->rolePermissions = array_diff($this->rolePermissions, [$permissionName]);
        } else {
            // Find the permission group
            $permissionInfo = AccessPermission::where('permission_name', $permissionName)->first();
            $groupId = $permissionInfo ? $permissionInfo->group_id : null;
            
            if (!$groupId) {
                // If permission doesn't exist yet, use the currently selected group
                $groupId = $this->selectedGroupId ?? PermissionGroup::first()->group_id;
            }
            
            // Add permission
            AccessPermission::create([
                'role_id' => $this->selectedRoleId,
                'permission_name' => $permissionName,
                'description' => $permissionInfo ? $permissionInfo->description : 'Added permission',
                'group_id' => $groupId
            ]);
            
            $this->rolePermissions[] = $permissionName;
        }
        
        session()->flash('success', 'Permissions updated successfully.');
    }
    
    public function createPermission()
    {
        $this->validate([
            'newPermissionName' => 'required|string|max:255|regex:/^[a-z_]+$/',
            'newPermissionDescription' => 'required|string|max:255',
            'newPermissionGroupId' => 'required|exists:permission_groups,group_id',
        ], [
            'newPermissionName.regex' => 'Permission name must only contain lowercase letters and underscores.'
        ]);
        
        // Check if permission already exists
        $existingPermission = AccessPermission::where('permission_name', $this->newPermissionName)->first();
        if ($existingPermission) {
            $this->addError('newPermissionName', 'This permission already exists.');
            return;
        }
        
        // Create permission for the selected role
        if ($this->selectedRoleId) {
            AccessPermission::create([
                'role_id' => $this->selectedRoleId,
                'permission_name' => $this->newPermissionName,
                'description' => $this->newPermissionDescription,
                'group_id' => $this->newPermissionGroupId
            ]);
            
            $this->rolePermissions[] = $this->newPermissionName;
            $this->resetNewPermissionForm();
            session()->flash('success', 'New permission created and assigned successfully.');
        }
    }
    
    public function resetNewPermissionForm()
    {
        $this->newPermissionName = '';
        $this->newPermissionDescription = '';
        $this->resetErrorBag();
    }
    
    public function render()
    {
        $roles = Role::all();
        
        $query = AccessPermission::query()
            ->with('permissionGroup');
            
        // Filter by selected group if any
        if ($this->selectedGroupId) {
            $query->where('group_id', $this->selectedGroupId);
        }
        
        // Filter by search term if any
        if ($this->searchPermission) {
            $query->where(function($q) {
                $q->where('permission_name', 'like', '%' . $this->searchPermission . '%')
                  ->orWhere('description', 'like', '%' . $this->searchPermission . '%');
            });
        }
        
        // Get unique permissions by name (to avoid showing duplicates across roles)
        $uniquePermissions = $query->get()
            ->unique('permission_name')
            ->sortBy('permission_name');
        
        // Group permissions by their group for easier display
        $permissionsByGroup = $uniquePermissions->groupBy(function($permission) {
            return $permission->permissionGroup->group_name ?? 'Uncategorized';
        });
        
        return view('livewire.admin.permission-management', [
            'roles' => $roles,
            'permissionsByGroup' => $permissionsByGroup
        ]);
    }
}
