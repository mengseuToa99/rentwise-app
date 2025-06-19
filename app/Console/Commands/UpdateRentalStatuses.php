<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use App\Models\Unit;
use Carbon\Carbon;

class UpdateRentalStatuses extends Command
{
    protected $signature = 'rentals:update-statuses';
    protected $description = 'Update rental statuses and unit statuses based on expiration dates';

    public function handle()
    {
        $this->info('Starting rental status update...');

        // Update expired rentals
        $expiredRentals = Rental::where('status', 'active')
            ->where('end_date', '<', now())
            ->get();

        $this->info("Found {$expiredRentals->count()} expired rentals");

        foreach ($expiredRentals as $rental) {
            $rental->status = 'expired';
            $rental->save();

            // Update unit status to vacant
            $unit = Unit::find($rental->room_id);
            if ($unit) {
                $unit->available = true;
                $unit->status = 'vacant';
                $unit->save();
                $this->info("Updated unit {$unit->room_number} to vacant (rental {$rental->rental_id} expired)");
            }
        }

        // Update active rentals that should be active
        $activeRentals = Rental::where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();

        foreach ($activeRentals as $rental) {
            // Ensure unit is marked as occupied
            $unit = Unit::find($rental->room_id);
            if ($unit && $unit->status !== 'occupied') {
                $unit->available = false;
                $unit->status = 'occupied';
                $unit->save();
                $this->info("Updated unit {$unit->room_number} to occupied (rental {$rental->rental_id} is active)");
            }
        }

        $this->info('Rental status update completed successfully!');
    }
} 