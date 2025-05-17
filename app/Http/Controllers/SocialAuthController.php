<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
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
            // Special case for Telegram
            if ($provider === 'telegram') {
                // For debugging purposes, show the test page directly
                return redirect()->route('telegram.test');
            }
            
            // For local development, disable SSL verification
            // WARNING: Only use this in development, never in production!
            if (app()->environment('local')) {
                $guzzle = new \GuzzleHttp\Client([
                    'verify' => false,
                ]);
                $driver = Socialite::driver($provider)->setHttpClient($guzzle);
                return $driver->redirect();
            } else {
                return Socialite::driver($provider)->redirect();
            }
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
            // Special case for Telegram
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
     * Handle Telegram callback - we'll handle this differently
     * since we're using direct Telegram integration
     */
    private function handleTelegramCallback(Request $request)
    {
        // Implement a webhook for Telegram bot to handle login
        // This is a placeholder - you'll need to set up a Telegram webhook
        // that receives messages and authenticates users
        
        // For now, we'll just log the attempt and return an error
        Log::info('Telegram callback attempt', $request->all());
        
        return redirect()->route('login')->with('error', 'Telegram login not fully implemented yet. Please use another method.');
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
    
    /**
     * Verify Telegram login token
     */
    public function verifyTelegramToken($token)
    {
        // Try to get the cached telegram auth data
        $telegramData = Cache::get("telegram_auth_{$token}");
        
        if (!$telegramData) {
            return redirect()->route('login')->with('error', 'Invalid or expired login link. Please try again.');
        }
        
        // Log the data for debugging
        Log::info('Telegram login data', $telegramData);
        
        // Create a user object from Telegram data
        $socialUser = (object)[
            'id' => $telegramData['id'],
            'name' => $telegramData['first_name'] . ' ' . $telegramData['last_name'],
            'nickname' => $telegramData['username'] ?? null,
            'email' => null // Telegram doesn't provide email
        ];
        
        // Find or create user
        $user = $this->findOrCreateUser($socialUser, 'telegram');
        
        // Login
        Auth::login($user, true);
        
        // Delete the token from cache
        Cache::forget("telegram_auth_{$token}");
        
        return redirect()->intended(route('dashboard'));
    }
    
    /**
     * Verify and handle Telegram widget authentication
     */
    public function verifyTelegramWidget(Request $request)
    {
        // Log all data for debugging
        Log::info('Telegram widget data received', $request->all());
        
        $data = $request->all();
        
        // Verify the hash (security check)
        if (isset($data['hash'])) {
            $checkHash = $data['hash'];
            unset($data['hash']);
            
            // Sort the array by key
            ksort($data);
            
            // Create the data check string
            $dataCheckString = '';
            foreach ($data as $key => $value) {
                $dataCheckString .= "$key=$value\n";
            }
            $dataCheckString = rtrim($dataCheckString, "\n");
            
            // Create the secret key by hashing the bot token
            $botToken = config('services.telegram.client_secret', env('TELEGRAM_TOKEN'));
            $secretKey = hash('sha256', $botToken, true);
            
            // Calculate the hash
            $hash = hash_hmac('sha256', $dataCheckString, $secretKey);
            
            if (strcmp($hash, $checkHash) !== 0) {
                Log::warning('Invalid Telegram hash', [
                    'calculated' => $hash,
                    'received' => $checkHash
                ]);
                return response()->json(['success' => false, 'message' => 'Invalid authentication data']);
            }
        } else {
            Log::warning('No hash provided in Telegram data');
            return response()->json(['success' => false, 'message' => 'Missing authentication data']);
        }
        
        // Check if auth date is not older than 1 day
        if (isset($data['auth_date']) && (time() - $data['auth_date'] > 86400)) {
            return response()->json(['success' => false, 'message' => 'Authentication data expired']);
        }
        
        // Create a user object from Telegram data
        $socialUser = (object)[
            'id' => $data['id'],
            'name' => $data['first_name'] . ' ' . ($data['last_name'] ?? ''),
            'nickname' => $data['username'] ?? null,
            'email' => null // Telegram doesn't provide email
        ];
        
        // Find or create user
        $user = $this->findOrCreateUser($socialUser, 'telegram');
        
        // Login
        Auth::login($user, true);
        
        return response()->json(['success' => true, 'redirect' => route('dashboard')]);
    }
} 