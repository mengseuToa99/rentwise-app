<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramSetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook {--url= : The webhook URL, defaults to APP_URL/api/telegram/webhook}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the webhook URL for the Telegram bot';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botToken = config('services.telegram.client_secret', env('TELEGRAM_TOKEN'));
        
        if (!$botToken) {
            $this->error('Telegram bot token not found! Make sure TELEGRAM_TOKEN is set in your .env file.');
            return 1;
        }
        
        $webhookUrl = $this->option('url') ?: url('/api/telegram/webhook');
        
        $this->info("Setting webhook URL to: {$webhookUrl}");
        
        try {
            // For local development, disable SSL verification
            $options = [];
            if (app()->environment('local')) {
                $options['verify'] = false;
            }
            
            $response = Http::withOptions($options)
                ->post("https://api.telegram.org/bot{$botToken}/setWebhook", [
                    'url' => $webhookUrl,
                ]);
            
            if ($response->successful() && $response->json('ok')) {
                $this->info('Webhook set successfully!');
                $this->table(['Setting', 'Value'], [
                    ['URL', $webhookUrl],
                    ['Result', 'Success'],
                    ['Response', json_encode($response->json())]
                ]);
                return 0;
            } else {
                $this->error('Failed to set webhook!');
                $this->error('Response: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error setting webhook: ' . $e->getMessage());
            Log::error('Telegram webhook setup error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
