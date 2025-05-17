<?php

namespace App\Services\Auth;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TelegramProvider extends AbstractProvider implements ProviderInterface
{
    protected $parameters = ['id', 'first_name', 'username', 'photo_url', 'auth_date', 'hash'];
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        // Get configuration from services.php
        $this->clientId = Config::get('services.telegram.bot_id');
        $this->clientSecret = Config::get('services.telegram.token');
        
        // Set the default configuration for the abstract provider
        parent::__construct(request(), '', '', '');
    }

    /**
     * Get the authentication configuration for the provider.
     *
     * @return array
     */
    protected function getConfig()
    {
        return array_merge([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect' => $this->redirectUrl,
        ], $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return 'https://oauth.telegram.org/auth';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://oauth.telegram.org/auth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        // This is handled differently with Telegram
        // as we get the user data directly from the request
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'] ?? null,
            'nickname' => $user['username'] ?? null,
            'name' => $user['first_name'] ?? null,
            'avatar' => $user['photo_url'] ?? null,
        ]);
    }

    /**
     * Validates Telegram login widget data
     *
     * @param array $data
     * @return User|null
     */
    public function validateTelegramData(array $data)
    {
        // Log incoming data for debugging
        Log::info("Received Telegram data:", ['data' => $data]);
        
        // Check all required fields except hash (which we'll validate separately)
        foreach ($this->parameters as $parameter) {
            if ($parameter !== 'hash' && !isset($data[$parameter])) {
                Log::error("Missing parameter in Telegram data: {$parameter}");
                return null;
            }
        }

        // Generate data check string
        $check_string = '';
        foreach ($data as $key => $value) {
            if ($key !== 'hash') {
                $check_string .= $key . '=' . $value . "\n";
            }
        }
        $check_string = trim($check_string);
        
        Log::debug("Telegram check string: {$check_string}");

        // Generate secret key
        $secret_key = hash('sha256', $this->clientSecret, true);
        Log::debug("Telegram token used for hash: {$this->clientSecret}");

        // Generate hash
        $hash = hash_hmac('sha256', $check_string, $secret_key);
        Log::debug("Generated hash: {$hash}, Received hash: " . ($data['hash'] ?? 'none'));

        // Compare hashes
        if (!isset($data['hash']) || strcmp($hash, $data['hash']) !== 0) {
            Log::error("Telegram hash validation failed");
            return null;
        }

        // Check auth date (Telegram auth is valid for 1 day)
        if ((time() - $data['auth_date']) > 86400) {
            Log::error("Telegram auth date expired");
            return null;
        }

        // Create user
        $user = $this->mapUserToObject($data);
        Log::info("Telegram user authenticated successfully", ['id' => $user->id, 'name' => $user->name]);
        return $user;
    }
} 