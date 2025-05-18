<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utility;
use App\Models\UtilityPrice;

class UtilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default utilities
        $utilities = [
            [
                'utility_name' => 'Electricity',
                'description' => 'Electricity consumption measured in kilowatt-hours (kWh)'
            ],
            [
                'utility_name' => 'Water',
                'description' => 'Water consumption measured in cubic meters (m³)'
            ]
        ];

        foreach ($utilities as $utilityData) {
            $utility = Utility::firstOrCreate(
                ['utility_name' => $utilityData['utility_name']],
                ['description' => $utilityData['description']]
            );

            // Add default pricing for each utility
            if ($utility->wasRecentlyCreated) {
                $defaultPrice = $utility->utility_name === 'Electricity' ? 0.15 : 2.50;
                UtilityPrice::create([
                    'utility_id' => $utility->utility_id,
                    'price' => $defaultPrice,
                    'effective_date' => now(),
                ]);
            }
        }
    }
} 