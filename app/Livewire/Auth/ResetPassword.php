<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.guest')]
class ResetPassword extends Component
{
    #[Locked]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        try {
            $this->validate([
                'token' => ['required'],
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/', 'confirmed'],
                'password_confirmation' => ['required', 'same:password'],
            ], [
                'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character.',
                'password_confirmation.same' => 'The password confirmation does not match.',
            ]);

            $status = Password::reset(
                $this->only('email', 'password', 'password_confirmation', 'token'),
                function ($user) {
                    $user->forceFill([
                        'password_hash' => Hash::make($this->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status != Password::PASSWORD_RESET) {
                $this->addError('email', __($status));
                return;
            }

            session()->flash('status', 'Your password has been reset successfully. You can now login with your new password.');
            $this->redirectRoute('login', navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while resetting your password. Please try again.');
            $this->addError('email', $e->getMessage());
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'],
            'password_confirmation' => ['required', 'same:password'],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character.',
            'password_confirmation.same' => 'The password confirmation does not match.',
        ]);
    }
}
