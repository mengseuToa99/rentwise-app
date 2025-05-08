<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;

class UpdateRentalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set all existing rentals to 'active' status
        DB::table('rental_details')
            ->whereNull('status')
            ->update(['status' => 'active']);
        
        // For rentals with end_date in the past, set to 'expired'
        DB::table('rental_details')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->update(['status' => 'expired']);
            
        $this->command->info('All rental statuses have been updated.');
    }
}
