<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;
use App\Models\User;

class RoleManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $selectedRole = null;
    public $isModalOpen = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    
    // Form fields
    public $role_name;
    public $description;
    public $parent_role_id;
    
    protected $rules = [
        'role_name' => 'required|string|max:50',
        'description' => 'required|string|max:255',
        'parent_role_id' => 'nullable|exists:roles,role_id',
    ];
    
    public function mount()
    {
        $this->resetForm();
    }
    
    public function render()
    {
        $roles = Role::when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('role_name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount('users')
            ->paginate(10);
            
        $parentRoles = Role::all();
        
        return view('livewire.admin.role-management', [
            'roles' => $roles,
            'parentRoles' => $parentRoles
        ])->layout('layouts.admin');
    }
    
    public function resetForm()
    {
        $this->reset(['role_name', 'description', 'parent_role_id']);
        $this->resetErrorBag();
    }
    
    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->isModalOpen = true;
    }
    
    public function openEditModal($roleId)
    {
        $this->resetForm();
        $this->modalMode = 'edit';
        
        $role = Role::find($roleId);
        if ($role) {
            $this->selectedRole = $role;
            $this->role_name = $role->role_name;
            $this->description = $role->description;
            $this->parent_role_id = $role->parent_role_id;
        }
        
        $this->isModalOpen = true;
    }
    
    public function closeModal()
    {
        $this->isModalOpen = false;
    }
    
    public function saveRole()
    {
        if ($this->modalMode === 'create') {
            $this->validate(array_merge($this->rules, [
                'role_name' => 'required|string|max:50|unique:roles,role_name',
            ]));
            
            Role::create([
                'role_name' => $this->role_name,
                'description' => $this->description,
                'parent_role_id' => $this->parent_role_id,
            ]);
            
            session()->flash('success', 'Role created successfully.');
        } else {
            $this->validate(array_merge($this->rules, [
                'role_name' => 'required|string|max:50|unique:roles,role_name,' . $this->selectedRole->role_id . ',role_id',
            ]));
            
            // Check for circular parent-child relationship
            if ($this->parent_role_id == $this->selectedRole->role_id) {
                $this->addError('parent_role_id', 'A role cannot be its own parent.');
                return;
            }
            
            $this->selectedRole->update([
                'role_name' => $this->role_name,
                'description' => $this->description,
                'parent_role_id' => $this->parent_role_id,
            ]);
            
            session()->flash('success', 'Role updated successfully.');
        }
        
        $this->closeModal();
        $this->resetForm();
    }
    
    public function deleteRole($roleId)
    {
        try {
            $role = Role::find($roleId);
            
            if ($role) {
                // Check if role has users
                $usersCount = $role->users()->count();
                if ($usersCount > 0) {
                    session()->flash('error', 'Cannot delete role. It is assigned to ' . $usersCount . ' user(s).');
                    return;
                }
                
                // Check if role has child roles
                $childRolesCount = Role::where('parent_role_id', $roleId)->count();
                if ($childRolesCount > 0) {
                    session()->flash('error', 'Cannot delete role. It is a parent to ' . $childRolesCount . ' other role(s).');
                    return;
                }
                
                // Delete the role
                $role->delete();
                session()->flash('success', 'Role deleted successfully.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting role: ' . $e->getMessage());
        }
    }
}
