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
            
            // Debug log the hash details
            Log::info('Login attempt details', [
                'email' => $user->email,
                'password_hash_exists' => !empty($user->password_hash),
                'password_hash_length' => strlen($user->password_hash),
                'password_hash_format' => substr($user->password_hash, 0, 4)
            ]);
            
            // Check if password hash exists and has the right format
            if (empty($user->password_hash) || strlen($user->password_hash) < 20) {
                Log::error('Invalid password hash format', [
                    'email' => $user->email,
                    'hash_length' => strlen($user->password_hash)
                ]);
                session()->flash('error', 'Account password is not properly set. Please use the password reset link.');
                return;
            }
            
            // Direct password verification with diagnostics
            $passwordCorrect = Hash::check($this->password, $user->password_hash);
            
            Log::info('Password verification result', [
                'email' => $user->email,
                'result' => $passwordCorrect ? 'Correct' : 'Incorrect'
            ]);
            
            if ($passwordCorrect) {
                // Update login stats
                $user->last_login = now();
                $user->failed_login_attempts = 0;
                $user->save();
                
                // Manual authentication
                Auth::login($user, $this->remember);
                
                // Log success
                Log::info('Login successful', [
                    'email' => $user->email,
                    'user_id' => $user->user_id
                ]);
                
                // Redirect to dashboard
                $this->redirectRoute('dashboard');
                return;
            }
            
            // Increment failed login attempts
            $user->failed_login_attempts = $user->failed_login_attempts + 1;
            $user->save();
            
            // Detailed error message
            session()->flash('error', 'Invalid password. Please try again or use the "Forgot password" link.');
            
        } catch (\Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'An error occurred during login. Please try again.');
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
