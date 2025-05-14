<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;
    
    public $userId;
    public $username;
    public $email;
    public $firstName;
    public $lastName;
    public $phoneNumber;
    public $profileImage;
    public $newProfileImage;
    public $currentPassword;
    public $password;
    public $password_confirmation;
    public $roles = [];
    
    protected $rules = [
        'username' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|max:255',
        'firstName' => 'sometimes|string|max:255',
        'lastName' => 'sometimes|string|max:255',
        'phoneNumber' => 'sometimes|string|max:20',
        'currentPassword' => 'sometimes|required_with:password',
        'password' => 'sometimes|min:8|confirmed',
    ];
    
    public function mount()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $this->userId = $user->user_id;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->firstName = $user->first_name;
        $this->lastName = $user->last_name;
        $this->phoneNumber = $user->phone_number;
        $this->profileImage = $user->profile_picture;
        $this->roles = $user->roles ? $user->roles->pluck('role_name')->toArray() : [];
    }
    
    public function updateProfile()
    {
        $this->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
        ]);
        
        $user = Auth::user();
        
        if (!$user) {
            session()->flash('error', 'User not authenticated');
            return;
        }
        
        $user->username = $this->username;
        $user->email = $this->email;
        $user->first_name = $this->firstName;
        $user->last_name = $this->lastName;
        $user->phone_number = $this->phoneNumber;
        
        if ($this->newProfileImage) {
            $imagePath = $this->newProfileImage->store('profile', 'public');
            $user->profile_picture = $imagePath;
            $this->profileImage = $imagePath;
        }
        
        $user->save();
        
        session()->flash('success', 'Profile updated successfully');
    }
    
    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        if (!$user) {
            session()->flash('error', 'User not authenticated');
            return;
        }
        
        // Verify current password
        if (!Hash::check($this->currentPassword, $user->password_hash)) {
            session()->flash('error', 'Current password is incorrect');
            return;
        }
        
        $user->password_hash = Hash::make($this->password);
        $user->save();
        
        $this->reset(['currentPassword', 'password', 'password_confirmation']);
        
        session()->flash('success', 'Password updated successfully');
    }
    
    public function render()
    {
        // If user is admin, use admin layout, otherwise use default
        $layout = 'components.layouts.app';
        if (Auth::user()->roles->contains('role_name', 'admin')) {
            $layout = 'layouts.admin';
        }
        
        return view('livewire.profile')
            ->layout($layout);
    }
} 