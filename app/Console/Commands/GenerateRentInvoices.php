<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateRentInvoices extends Command
{
    protected $signature = 'invoices:generate-rent';
    protected $description = 'Generate rent invoices for upcoming due dates';

    public function handle()
    {
        $this->info('Starting rent invoice generation...');

        // Get active rentals
        $activeRentals = Rental::where('status', 'active')
            ->where('end_date', '>', now())
            ->get();

        foreach ($activeRentals as $rental) {
            // Check if an invoice already exists for this month
            $existingInvoice = Invoice::where('rental_id', $rental->rental_id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->first();

            if (!$existingInvoice) {
                try {
                    // Calculate due date (15 days from now)
                    $dueDate = now()->addDays(15);

                    // Create the invoice
                    Invoice::create([
                        'rental_id' => $rental->rental_id,
                        'amount_due' => $rental->unit->rent_amount,
                        'due_date' => $dueDate,
                        'paid' => false,
                        'payment_method' => 'cash', // Default payment method
                        'payment_status' => 'pending',
                        'description' => 'Monthly Rent - ' . now()->format('F Y')
                    ]);

                    $this->info("Generated invoice for rental ID: {$rental->rental_id}");
                    Log::info("Generated rent invoice for rental ID: {$rental->rental_id}");
                } catch (\Exception $e) {
                    $this->error("Failed to generate invoice for rental ID: {$rental->rental_id}");
                    Log::error("Failed to generate rent invoice: " . $e->getMessage());
                }
            }
        }

        $this->info('Rent invoice generation completed.');
    }
} 