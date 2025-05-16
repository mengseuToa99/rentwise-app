<?php

namespace App\Services\Auth;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class TelegramProvider extends AbstractProvider implements ProviderInterface
{
    protected $parameters = ['bot_id', 'username', 'photo_url', 'auth_date', 'hash'];

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
        // Check all required fields
        foreach ($this->parameters as $parameter) {
            if (!isset($data[$parameter])) {
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

        // Generate secret key
        $secret_key = hash('sha256', $this->clientSecret, true);

        // Generate hash
        $hash = hash_hmac('sha256', $check_string, $secret_key);

        // Compare hashes
        if (strcmp($hash, $data['hash']) !== 0) {
            return null;
        }

        // Check auth date (Telegram auth is valid for 1 day)
        if ((time() - $data['auth_date']) > 86400) {
            return null;
        }

        // Create user
        $user = $this->mapUserToObject($data);
        return $user;
    }
} 