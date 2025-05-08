<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'tenant'; // Default role
    public string $first_name = '';
    public string $last_name = '';
    public string $error_message = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        try {
            $name_parts = explode(' ', $this->name, 2);
            $this->first_name = $name_parts[0];
            $this->last_name = isset($name_parts[1]) ? $name_parts[1] : $name_parts[0]; // Default to first name if no last name provided

            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string', 'in:tenant,landlord,admin'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
            ]);

            // Create User
            $username = strtolower(str_replace(' ', '.', $validated['name']));
            $user = User::create([
                'username' => $username,
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'status' => 'active'
            ]);
            
            event(new Registered($user));
            
            Log::info('User created successfully', ['user_id' => $user->user_id, 'email' => $user->email]);
            
            // Check if we have the required roles in the database
            $rolesCount = Role::count();
            if ($rolesCount == 0) {
                // Create basic roles if they don't exist
                Role::create(['role_name' => 'admin', 'description' => 'Administrator']);
                Role::create(['role_name' => 'landlord', 'description' => 'Property Owner']);
                Role::create(['role_name' => 'tenant', 'description' => 'Property Renter']);
                Log::info('Created default roles as none existed');
            }
            
            // Get role and assign it to user
            $role = Role::where('role_name', $validated['role'])->first();
            
            if ($role) {
                UserRole::create([
                    'user_id' => $user->user_id,
                    'role_id' => $role->role_id
                ]);
                Log::info('Role assigned successfully', ['role' => $role->role_name]);
            } else {
                Log::error('Role not found', ['requested_role' => $validated['role']]);
                $this->error_message = 'Role assignment failed. Please contact support.';
                // Create a default role assignment as tenant
                $defaultRole = Role::where('role_name', 'tenant')->first();
                if ($defaultRole) {
                    UserRole::create([
                        'user_id' => $user->user_id,
                        'role_id' => $defaultRole->role_id
                    ]);
                    Log::info('Default role (tenant) assigned as fallback');
                }
            }

            Auth::login($user);
            Log::info('User logged in successfully', ['user_id' => $user->user_id]);

            $this->redirect(route('dashboard', absolute: false), navigate: true);
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error_message = 'Registration failed: ' . $e->getMessage();
            session()->flash('error', $this->error_message);
        }
    }
    
    public function render()
    {
        return view('livewire.auth.register');
    }
}
