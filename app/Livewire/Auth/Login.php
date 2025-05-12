<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        try {
            // Find the user by email
            $user = User::where('email', $this->email)->first();
            
            if (!$user) {
                session()->flash('error', 'User not found');
                return;
            }
            
            // Check status first
            if ($user->status !== 'active') {
                session()->flash('error', 'Account is not active');
                return;
            }
            
            // Safely log user roles if they exist
            $roles = $user->roles ? $user->roles->pluck('role_name') : collect(['none']);
            Log::debug('Login attempt', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'has_roles' => $user->roles !== null,
                'roles' => $roles
            ]);
            
            // Direct password verification
            if (Hash::check($this->password, $user->password_hash)) {
                // Update login stats
                $user->last_login = now();
                $user->failed_login_attempts = 0;
                $user->save();
                
                // Manual authentication
                Auth::login($user, $this->remember);
                
                // Safely log authenticated user info
                $authUser = Auth::user();
                $authRoles = $authUser && $authUser->roles ? $authUser->roles->pluck('role_name') : collect(['none']);
                
                Log::debug('User authenticated successfully', [
                    'user_id' => Auth::id(),
                    'has_roles' => $authUser && $authUser->roles !== null,
                    'roles' => $authRoles
                ]);
                
                // Redirect to dashboard
                redirect()->intended(route('dashboard'));
                return;
            }
            
            // Increment failed login attempts
            $user->failed_login_attempts = $user->failed_login_attempts + 1;
            $user->save();
            
            session()->flash('error', 'Invalid credentials');
            
        } catch (\Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'An error occurred during login');
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
