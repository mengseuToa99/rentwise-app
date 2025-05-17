<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Http\Request;

class TelegramPollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:poll {--timeout=60 : Polling timeout in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll Telegram for updates (for local development)';

    /**
     * The last update ID processed.
     *
     * @var int
     */
    protected $lastUpdateId = 0;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeout = $this->option('timeout');
        $botToken = config('services.telegram.client_secret', env('TELEGRAM_TOKEN'));
        
        if (!$botToken) {
            $this->error('Telegram bot token not found! Make sure TELEGRAM_TOKEN is set in your .env file.');
            return 1;
        }
        
        $this->info("Starting Telegram polling for {$timeout} seconds...");
        $this->info("Press Ctrl+C to stop");
        
        $startTime = time();
        $webhookController = app(TelegramWebhookController::class);
        
        try {
            while (time() - $startTime < $timeout) {
                // Get updates from Telegram
                $response = Http::withOptions(['verify' => false])
                    ->get("https://api.telegram.org/bot{$botToken}/getUpdates", [
                        'offset' => $this->lastUpdateId + 1,
                        'timeout' => 5,
                    ]);
                
                if (!$response->successful()) {
                    $this->error("Failed to get updates: " . $response->body());
                    sleep(3);
                    continue;
                }
                
                $updates = $response->json('result', []);
                
                if (count($updates) > 0) {
                    $this->info("Received " . count($updates) . " updates");
                    
                    foreach ($updates as $update) {
                        $this->processUpdate($update, $webhookController);
                        $this->lastUpdateId = $update['update_id'];
                    }
                } else {
                    $this->comment("No new updates...");
                }
                
                // Wait 2 seconds before polling again
                sleep(2);
            }
            
            $this->info("Polling complete");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            Log::error('Telegram polling error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
    
    /**
     * Process a single update by sending it to our webhook controller
     */
    protected function processUpdate($update, $webhookController)
    {
        $updateId = $update['update_id'];
        
        $this->line("Processing update #{$updateId}");
        
        try {
            // Create a request object with the update data
            $request = Request::create(
                '/api/telegram/webhook',
                'POST',
                [],
                [],
                [],
                [],
                json_encode($update)
            );
            
            // Set JSON headers
            $request->headers->set('Content-Type', 'application/json');
            
            // Call the webhook controller directly
            $response = $webhookController->handleWebhook($request);
            
            // Log the response
            $this->info("Response: " . $response->getContent());
            
        } catch (\Exception $e) {
            $this->error("Failed to process update: " . $e->getMessage());
            Log::error('Telegram update processing error', [
                'update_id' => $updateId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
