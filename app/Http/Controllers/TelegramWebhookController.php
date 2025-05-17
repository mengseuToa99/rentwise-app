<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class TelegramWebhookController extends Controller
{
    protected $botToken;
    protected $botUsername;
    
    public function __construct()
    {
        $this->botToken = config('services.telegram.client_secret', env('TELEGRAM_TOKEN'));
        $this->botUsername = config('services.telegram.bot', env('TELEGRAM_BOT_ID'));
        
        if (empty($this->botToken)) {
            Log::error('Telegram bot token is not configured');
        }
        
        if (empty($this->botUsername)) {
            Log::error('Telegram bot username is not configured');
        }
    }
    
    /**
     * Handle the incoming webhook from Telegram
     */
    public function handleWebhook(Request $request)
    {
        // Log the webhook data
        Log::info('Telegram webhook received', $request->all());
        
        try {
            // Get the update from Telegram
            $update = $request->all();
            
            // Check if this is a message
            if (isset($update['message'])) {
                return $this->handleMessage($update['message']);
            } else {
                Log::warning('Unknown update type received', $update);
                return response()->json(['status' => 'unknown_update_type']);
            }
            
        } catch (\Exception $e) {
            Log::error('Error handling Telegram webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Handle incoming message
     */
    private function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        
        // Log the incoming message for debugging
        Log::info('Handling Telegram message', [
            'chat_id' => $chatId,
            'text' => $text,
            'from' => $message['from'] ?? 'unknown'
        ]);
        
        // Check if this is a start command with auth parameter
        if (strpos($text, '/start auth') === 0) {
            return $this->handleAuthRequest($message);
        }
        
        // For simple /start command
        if ($text === '/start') {
            $firstName = $message['from']['first_name'] ?? 'there';
            
            $this->sendTelegramMessage($chatId, 
                "👋 <b>Hello, {$firstName}!</b>\n\n" .
                "Welcome to the Rentwise App Bot. I can help you log in to the application.\n\n" .
                "Send <code>/start auth</code> when you're ready to log in."
            );
            
            Log::info('Sent welcome message', ['chat_id' => $chatId]);
            return response()->json(['status' => 'success', 'message' => 'welcome_sent']);
        }
        
        // Default response for any other message
        $this->sendTelegramMessage($chatId, 
            "Hello! I'm the Rentwise bot. Send /start auth to login to the website."
        );
        
        return response()->json(['status' => 'success', 'message' => 'default_response_sent']);
    }
    
    /**
     * Handle authentication request
     */
    private function handleAuthRequest($message)
    {
        $chatId = $message['chat']['id'];
        $userId = $message['from']['id'];
        $firstName = $message['from']['first_name'];
        $lastName = $message['from']['last_name'] ?? '';
        $username = $message['from']['username'] ?? '';
        
        // Generate a unique login token
        $token = Str::random(32);
        
        // Store token in cache with Telegram user info (expires in 10 minutes)
        Cache::put("telegram_auth_{$token}", [
            'id' => $userId,
            'chat_id' => $chatId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
        ], 600);
        
        // Generate login URL
        $loginUrl = url("/auth/telegram/verify/{$token}");
        
        // Send message with login link
        $this->sendTelegramMessage($chatId, 
            "👋 <b>Hello, {$firstName}!</b>\n\n" .
            "Click the button below to log in to Rentwise:\n\n" .
            "<a href=\"{$loginUrl}\">🔐 Log in to Rentwise</a>\n\n" .
            "<i>This login link will expire in 10 minutes.</i>"
        );
        
        return response()->json(['status' => 'success']);
    }
    
    /**
     * Send message to Telegram chat
     */
    private function sendTelegramMessage($chatId, $text)
    {
        Log::info('Sending Telegram message', [
            'chat_id' => $chatId,
            'text' => $text
        ]);
        
        try {
            $response = Http::withOptions(['verify' => false])
                ->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                ]);
            
            if (!$response->successful()) {
                Log::error('Failed to send Telegram message', [
                    'response' => $response->json(),
                    'chat_id' => $chatId,
                    'status_code' => $response->status()
                ]);
                return false;
            }
            
            Log::info('Telegram message sent successfully', [
                'chat_id' => $chatId,
                'message_id' => $response->json('result.message_id') ?? 'unknown'
            ]);
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Exception sending Telegram message', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId
            ]);
            return false;
        }
    }
    
    /**
     * Set webhook URL for the Telegram bot
     */
    public function setWebhook()
    {
        $webhookUrl = url('/api/telegram/webhook');
        
        $response = Http::post("https://api.telegram.org/bot{$this->botToken}/setWebhook", [
            'url' => $webhookUrl,
        ]);
        
        return response()->json($response->json());
    }
} 