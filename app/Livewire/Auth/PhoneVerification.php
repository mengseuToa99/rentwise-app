<?php

namespace App\Livewire\Auth;

use App\Services\Auth\PhoneAuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class PhoneVerification extends Component
{
    #[Validate('required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10')]
    public string $phone = '';

    #[Validate('required|string|size:6')]
    public string $otp = '';

    public bool $otpSent = false;

    /**
     * Send OTP to the phone number
     */
    public function sendOTP()
    {
        $this->validate([
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10'
        ]);

        try {
            $phoneAuthService = new PhoneAuthService();
            $otp = $phoneAuthService->generateOTP($this->phone);
            
            // In a production app, you would integrate with an SMS service
            // For now, we'll just log the OTP
            Log::info("OTP for phone {$this->phone}: {$otp}");
            
            $this->otpSent = true;
            session()->flash('success', 'OTP sent to your phone.');
            
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'error' => $e->getMessage(),
                'phone' => $this->phone
            ]);
            
            session()->flash('error', 'Failed to send OTP. Please try again.');
        }
    }

    /**
     * Verify OTP and login the user
     */
    public function verifyOTP()
    {
        $this->validate([
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'otp' => 'required|string|size:6'
        ]);

        try {
            $phoneAuthService = new PhoneAuthService();
            
            // Validate OTP
            if (!$phoneAuthService->validateOTP($this->phone, $this->otp)) {
                session()->flash('error', 'Invalid OTP. Please try again.');
                return;
            }
            
            // Find or create the user
            $user = $phoneAuthService->findOrCreateUser($this->phone);
            
            // Login the user
            Auth::login($user, true);
            
            // Redirect to dashboard
            redirect()->intended(route('dashboard'));
            
        } catch (\Exception $e) {
            Log::error('Failed to verify OTP', [
                'error' => $e->getMessage(),
                'phone' => $this->phone
            ]);
            
            session()->flash('error', 'Failed to verify OTP. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.auth.phone-verification');
    }
} 