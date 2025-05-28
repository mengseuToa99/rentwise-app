<?php

namespace App\Livewire\Tenants;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateTenant extends Component
{
    public $username;
    public $email;
    public $password;
    public $password_confirmation;
    public $phone_number;
    public $first_name;
    public $last_name;
    public $status = 'active';

    protected $rules = [
        'username' => 'required|string|max:255|unique:users,username',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
        'password_confirmation' => 'required',
        'phone_number' => 'required|string|max:20',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
    ];

    public function render()
    {
        return view('livewire.tenants.create-tenant');
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => $this->username,
                'email' => $this->email,
                'password_hash' => Hash::make($this->password),
                'phone_number' => $this->phone_number,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'status' => $this->status,
            ]);

            $tenantRole = Role::where('role_name', 'tenant')->first();
            if (!$tenantRole) {
                throw new \Exception('Tenant role not found. Please contact support.');
            }

            $user->roles()->sync([$tenantRole->role_id]);

            DB::commit();

            session()->flash('success', 'Tenant created successfully.');
            return redirect()->route('tenants.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create tenant: ' . $e->getMessage());
        }
    }
} 