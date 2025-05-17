<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramDebugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:debug {--chat_id= : Optional chat ID to send a test message to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Telegram bot functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Telegram Bot Debug Tool');
        $this->info('======================');
        
        // Get the bot token from config
        $botToken = config('services.telegram.client_secret', env('TELEGRAM_TOKEN'));
        $botUsername = config('services.telegram.bot', env('TELEGRAM_BOT_ID'));
        
        // Check if token is available
        if (!$botToken) {
            $this->error('❌ Telegram bot token not found in config or .env');
            return 1;
        } else {
            $this->info('✅ Bot token found: ' . substr($botToken, 0, 5) . '...' . substr($botToken, -5));
        }
        
        // Check if username is available
        if (!$botUsername) {
            $this->warn('⚠️ Bot username not found in config or .env');
        } else {
            $this->info('✅ Bot username: ' . $botUsername);
        }
        
        // Get bot information
        $this->info("\nGetting bot information...");
        
        try {
            $response = Http::withOptions(['verify' => false])
                ->get("https://api.telegram.org/bot{$botToken}/getMe");
            
            if ($response->successful()) {
                $botInfo = $response->json('result');
                $this->info('✅ Bot exists and is accessible via API');
                $this->table(
                    ['Property', 'Value'],
                    [
                        ['ID', $botInfo['id'] ?? 'N/A'],
                        ['Username', $botInfo['username'] ?? 'N/A'],
                        ['First Name', $botInfo['first_name'] ?? 'N/A'],
                        ['Is Bot', $botInfo['is_bot'] ? 'Yes' : 'No'],
                    ]
                );
            } else {
                $this->error('❌ Failed to get bot information: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Exception getting bot information: ' . $e->getMessage());
            return 1;
        }
        
        // Check webhook status
        $this->info("\nChecking webhook status...");
        
        try {
            $response = Http::withOptions(['verify' => false])
                ->get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");
            
            if ($response->successful()) {
                $webhookInfo = $response->json('result');
                $hasWebhook = !empty($webhookInfo['url']);
                
                if ($hasWebhook) {
                    $this->info("✅ Webhook is set to: " . $webhookInfo['url']);
                    $this->info("   Pending updates: " . ($webhookInfo['pending_update_count'] ?? 0));
                } else {
                    $this->info("ℹ️ No webhook is set (using polling mode)");
                }
            } else {
                $this->error('❌ Failed to get webhook information: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Exception checking webhook: ' . $e->getMessage());
        }
        
        // Send a test message if chat_id is provided
        $chatId = $this->option('chat_id');
        if ($chatId) {
            $this->info("\nSending test message to chat ID: {$chatId}");
            
            try {
                $response = Http::withOptions(['verify' => false])
                    ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => "✅ Test message from Rentwise Bot Debug Tool\nTime: " . now(),
                        'parse_mode' => 'HTML',
                    ]);
                
                if ($response->successful()) {
                    $this->info('✅ Test message sent successfully!');
                } else {
                    $this->error('❌ Failed to send test message: ' . $response->body());
                }
            } catch (\Exception $e) {
                $this->error('❌ Exception sending test message: ' . $e->getMessage());
            }
        }
        
        $this->info("\nDebug complete!");
        return 0;
    }
}
