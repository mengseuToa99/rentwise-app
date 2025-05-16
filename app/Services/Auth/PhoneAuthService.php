<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PhoneAuthService
{
    /**
     * Generate a 6-digit OTP for phone verification
     *
     * @param string $phone
     * @return string
     */
    public function generateOTP(string $phone): string
    {
        // Generate a 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for verification
        // Valid for 5 minutes
        Cache::put("phone_otp_$phone", $otp, 300);
        
        // In a real app, send the OTP via SMS
        // For development/testing, we just log it
        Log::info("Phone OTP generated for $phone: $otp");
        
        return $otp;
    }
    
    /**
     * Validate the OTP for a phone number
     *
     * @param string $phone
     * @param string $otp
     * @return bool
     */
    public function validateOTP(string $phone, string $otp): bool
    {
        $storedOTP = Cache::get("phone_otp_$phone");
        
        if (!$storedOTP || $storedOTP !== $otp) {
            return false;
        }
        
        // Clear the OTP after successful validation
        Cache::forget("phone_otp_$phone");
        
        return true;
    }
    
    /**
     * Find or create a user by phone number
     *
     * @param string $phone
     * @return User
     */
    public function findOrCreateUser(string $phone): User
    {
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            // Create a new user with phone number
            $user = new User();
            $user->phone = $phone;
            $user->username = "user_" . substr($phone, -4) . rand(100, 999);
            $user->first_name = "User";
            $user->last_name = substr($phone, -4);
            $user->email = "phone_$phone@example.com";
            $user->status = 'active';
            $user->password_hash = Hash::make(Str::random(32));
            $user->save();
        }
        
        return $user;
    }
} 