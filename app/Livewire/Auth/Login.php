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
use App\Traits\LogsActivity;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    use LogsActivity;

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
            // Find user by email
            $user = User::where('email', $this->email)->first();
            
            if (!$user) {
                // Log failed login attempt
                $this->logActivity('login_failed', "Failed login attempt for email: {$this->email} - User not found");
                
                session()->flash('error', 'These credentials do not match our records.');
                return;
            }
            
            // Check if user is active
            if ($user->status !== 'active') {
                // Log failed login attempt
                $this->logActivity('login_failed', "Failed login attempt for {$user->username} - Account not active (Status: {$user->status})", $user->user_id);
                
                session()->flash('error', 'Your account is not active. Please contact support.');
                return;
            }
            
            // Check password
            $passwordCorrect = Hash::check($this->password, $user->password_hash);
            
            if ($passwordCorrect) {
                // Update login stats
                $user->last_login = now();
                $user->failed_login_attempts = 0;
                $user->save();
                
                // Manual authentication
                Auth::login($user, $this->remember);
                
                // Log successful login
                $this->logActivity('login', "Successful login", $user->user_id);
                
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
            
            // Log failed login attempt
            $this->logActivity('login_failed', "Failed login attempt for {$user->username} - Invalid password", $user->user_id);
            
            // Detailed error message
            session()->flash('error', 'Invalid password. Please try again or use the "Forgot password" link.');
            
        } catch (\Exception $e) {
            // Log system error
            $this->logActivity('login_error', "System error during login attempt for email: {$this->email} - {$e->getMessage()}");
            
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
