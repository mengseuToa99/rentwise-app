<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $selectedUser = null;
    public $isModalOpen = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $selectedRoles = [];
    
    // Form fields
    public $username;
    public $email;
    public $password;
    public $phone_number;
    public $first_name;
    public $last_name;
    public $status = 'active';
    
    protected $rules = [
        'username' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'nullable|string|min:6',
        'phone_number' => 'nullable|string|max:20',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'status' => 'required|in:active,inactive,suspended',
        'selectedRoles' => 'required|array|min:1',
    ];
    
    protected $messages = [
        'selectedRoles.required' => 'Please select at least one role for the user.',
        'selectedRoles.min' => 'Please select at least one role for the user.',
    ];
    
    public function mount()
    {
        $this->resetForm();
    }
    
    public function render()
    {
        $users = User::when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('username', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->with('roles')
            ->paginate(10);
            
        $roles = Role::all();
        
        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => $roles
        ])->layout('layouts.admin');
    }
    
    public function resetForm()
    {
        $this->reset(['username', 'email', 'password', 'phone_number', 'first_name', 'last_name', 'status', 'selectedRoles']);
        $this->resetErrorBag();
    }
    
    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->isModalOpen = true;
    }
    
    public function openEditModal($userId)
    {
        $this->resetForm();
        $this->modalMode = 'edit';
        
        $user = User::with('roles')->find($userId);
        if ($user) {
            $this->selectedUser = $user;
            $this->username = $user->username;
            $this->email = $user->email;
            $this->phone_number = $user->phone_number;
            $this->first_name = $user->first_name;
            $this->last_name = $user->last_name;
            $this->status = $user->status;
            $this->selectedRoles = $user->roles->pluck('role_id')->toArray();
        }
        
        $this->isModalOpen = true;
    }
    
    public function closeModal()
    {
        $this->isModalOpen = false;
    }
    
    public function saveUser()
    {
        if ($this->modalMode === 'create') {
            $this->validate(array_merge($this->rules, [
                'password' => 'required|string|min:6',
                'email' => 'required|email|max:255|unique:users,email',
                'username' => 'required|string|max:255|unique:users,username',
            ]));
            
            $user = User::create([
                'username' => $this->username,
                'email' => $this->email,
                'password_hash' => Hash::make($this->password),
                'phone_number' => $this->phone_number,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'status' => $this->status,
            ]);
            
            $user->roles()->sync($this->selectedRoles);
            
            session()->flash('success', 'User created successfully.');
        } else {
            $this->validate(array_merge($this->rules, [
                'email' => 'required|email|max:255|unique:users,email,' . $this->selectedUser->user_id . ',user_id',
                'username' => 'required|string|max:255|unique:users,username,' . $this->selectedUser->user_id . ',user_id',
            ]));
            
            $this->selectedUser->update([
                'username' => $this->username,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'status' => $this->status,
            ]);
            
            if ($this->password) {
                $this->selectedUser->update([
                    'password_hash' => Hash::make($this->password),
                ]);
            }
            
            $this->selectedUser->roles()->sync($this->selectedRoles);
            
            session()->flash('success', 'User updated successfully.');
        }
        
        $this->closeModal();
        $this->resetForm();
    }
    
    public function deleteUser($userId)
    {
        try {
            $user = User::find($userId);
            if ($user) {
                // Remove roles first
                $user->roles()->detach();
                
                // Delete the user
                $user->delete();
                session()->flash('success', 'User deleted successfully.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}
