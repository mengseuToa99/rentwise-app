<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Auth\TelegramProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider
     */
    public function redirect(string $provider)
    {
        // Validate supported providers
        if (!in_array($provider, ['google', 'facebook', 'telegram'])) {
            return redirect()->route('login')->with('error', 'Unsupported login provider.');
        }
        
        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            Log::error("Social auth redirect error for provider $provider", [
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('login')->with('error', 'Failed to connect to login provider.');
        }
    }
    
    /**
     * Handle provider callback
     */
    public function callback(Request $request, string $provider)
    {
        // Validate supported providers
        if (!in_array($provider, ['google', 'facebook', 'telegram'])) {
            return redirect()->route('login')->with('error', 'Unsupported login provider.');
        }
        
        try {
            // Handle Telegram separately as it uses a different flow
            if ($provider === 'telegram') {
                return $this->handleTelegramCallback($request);
            }
            
            // For local development, disable SSL verification
            // WARNING: Only use this in development, never in production!
            if (app()->environment('local')) {
                $guzzle = new \GuzzleHttp\Client([
                    'verify' => false,
                ]);
                $driver = Socialite::driver($provider)->setHttpClient($guzzle);
                $socialUser = $driver->user();
            } else {
                // For other providers
                $socialUser = Socialite::driver($provider)->user();
            }
            
            // Find or create user
            $user = $this->findOrCreateUser($socialUser, $provider);
            
            // Login
            Auth::login($user, true);
            
            return redirect()->intended(route('dashboard'));
            
        } catch (\Exception $e) {
            Log::error("Social auth callback error for provider $provider", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')->with('error', 'Failed to authenticate with provider.');
        }
    }
    
    /**
     * Handle Telegram callback
     */
    private function handleTelegramCallback(Request $request)
    {
        $telegramProvider = new TelegramProvider();
        $telegramUser = $telegramProvider->validateTelegramData($request->all());
        
        if (!$telegramUser) {
            return redirect()->route('login')->with('error', 'Invalid Telegram authentication data.');
        }
        
        // Find or create user
        $user = $this->findOrCreateUser($telegramUser, 'telegram');
        
        // Login
        Auth::login($user, true);
        
        return redirect()->intended(route('dashboard'));
    }
    
    /**
     * Find or create user from social data
     */
    private function findOrCreateUser($socialUser, string $provider)
    {
        $providerId = $provider . '_id';
        
        // First try to find by provider and social ID
        $user = User::where($providerId, $socialUser->id)->first();
        
        // If not found, try to find by email
        if (!$user && $socialUser->email) {
            $user = User::where('email', $socialUser->email)->first();
            
            // If found by email, update the provider ID
            if ($user) {
                $user->$providerId = $socialUser->id;
                $user->save();
            }
        }
        
        // If still not found, create new user
        if (!$user) {
            // Split the name into first and last name
            $name = $socialUser->name ?? ($socialUser->nickname ?? 'User');
            $nameParts = explode(' ', $name);
            $firstName = $nameParts[0];
            $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
            
            // Create username from email or name
            $username = $socialUser->email 
                ? strtolower(explode('@', $socialUser->email)[0]) 
                : strtolower(str_replace(' ', '', $name)) . rand(100, 999);
            
            $user = new User();
            $user->username = $username;
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->email = $socialUser->email ?? $socialUser->id . '@' . $provider . '.user';
            $user->$providerId = $socialUser->id;
            $user->status = 'active';
            // Generate a random secure password for social login users
            $user->password_hash = Hash::make(Str::random(32));
            
            // Set profile picture if available
            if (isset($socialUser->avatar)) {
                $user->profile_picture = $socialUser->avatar;
            }
            
            $user->save();
        }
        
        return $user;
    }
} 