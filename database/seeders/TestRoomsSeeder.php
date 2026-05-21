<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Rental;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestRoomsSeeder extends Seeder
{
    public function run(): void
    {
        $landlord = $this->ensureLandlord();
        $property = $this->ensureProperty($landlord);
        $this->ensureRoomsAndTenants($landlord, $property);

        $this->command?->info("Seeded property '{$property->property_name}' with 20 rooms across 4 floors.");
    }

    private function ensureLandlord(): User
    {
        $landlord = User::where('email', 'landlord@example.com')->first();

        if (!$landlord) {
            $landlord = User::create([
                'username' => 'landlord',
                'email' => 'landlord@example.com',
                'password_hash' => Hash::make('password'),
                'phone_number' => '0123456789',
                'first_name' => 'Test',
                'last_name' => 'Landlord',
                'status' => 'active',
            ]);

            $landlordRole = Role::firstOrCreate(
                ['role_name' => 'landlord'],
                ['description' => 'Property Owner']
            );
            $landlord->roles()->attach($landlordRole->role_id);
        }

        return $landlord;
    }

    private function ensureProperty(User $landlord): Property
    {
        $property = Property::where('landlord_id', $landlord->user_id)
            ->where('property_name', 'Riverside 4-Floor Building')
            ->first();

        if ($property) {
            return $property;
        }

        return Property::create([
            'landlord_id' => $landlord->user_id,
            'property_name' => 'Riverside 4-Floor Building',
            'house_building_number' => '88',
            'street' => 'Sothearos Blvd',
            'village' => 'Tonle Bassac',
            'commune' => 'Chamkar Mon',
            'district' => 'Phnom Penh',
            'province' => 'Phnom Penh',
            'total_floors' => 4,
            'total_rooms' => 20,
            'description' => 'Test building with 20 rooms across 4 floors for batch invoice testing.',
            'status' => 'active',
            'property_type' => 'residential',
        ]);
    }

    private function ensureRoomsAndTenants(User $landlord, Property $property): void
    {
        $tenantRole = Role::firstOrCreate(
            ['role_name' => 'tenant'],
            ['description' => 'Property Renter']
        );

        $tenantNames = [
            ['Sophea', 'Chan'],     ['Dara', 'Pich'],       ['Veasna', 'Sok'],      ['Bopha', 'Ly'],
            ['Vannak', 'Heng'],     ['Srey', 'Mao'],        ['Rithy', 'Chea'],      ['Kunthea', 'Touch'],
            ['Channary', 'Sim'],    ['Sokha', 'Nuon'],      ['Mealea', 'Khat'],     ['Pisey', 'Phon'],
            ['Vichea', 'Hong'],     ['Theara', 'Sun'],      ['Reaksmey', 'Voeun'],  ['Sothea', 'Sao'],
            ['Sokunthea', 'Em'],    ['Visal', 'Kim'],       ['Chenda', 'Sar'],      ['Phally', 'Yan'],
        ];

        // 4 floors × 5 rooms per floor = 20 rooms
        // Leave 2 rooms vacant (mix of realism so batch flow can test selection)
        $vacantRooms = ['R203', 'R404'];
        $tenantIndex = 0;

        for ($floor = 1; $floor <= 4; $floor++) {
            for ($n = 1; $n <= 5; $n++) {
                $roomNumber = "R{$floor}0{$n}";
                $rentAmount = 200 + ($floor * 20) + ($n * 5);

                $unit = Unit::where('property_id', $property->property_id)
                    ->where('room_number', $roomNumber)
                    ->first();

                if (!$unit) {
                    $unit = Unit::create([
                        'property_id' => $property->property_id,
                        'room_number' => $roomNumber,
                        'floor_number' => $floor,
                        'room_name' => "Room {$roomNumber}",
                        'room_type' => $n <= 3 ? 'studio' : 'one_bedroom',
                        'description' => "Floor {$floor}, Room {$n}",
                        'available' => true,
                        'status' => 'vacant',
                        'rent_amount' => $rentAmount,
                        'due_date' => Carbon::now()->addMonth(),
                    ]);
                }

                if (in_array($roomNumber, $vacantRooms, true)) {
                    continue;
                }

                [$firstName, $lastName] = $tenantNames[$tenantIndex];
                $tenantIndex++;

                $email = strtolower($firstName . '.' . $lastName . '@test.local');
                $tenant = User::where('email', $email)->first();

                if (!$tenant) {
                    $tenant = User::create([
                        'username' => strtolower($firstName . $lastName),
                        'email' => $email,
                        'password_hash' => Hash::make('password'),
                        'phone_number' => '0' . random_int(11111111, 99999999),
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'status' => 'active',
                    ]);
                    $tenant->roles()->attach($tenantRole->role_id);
                }

                $existingRental = Rental::where('room_id', $unit->room_id)
                    ->where('status', 'active')
                    ->first();

                if (!$existingRental) {
                    Rental::create([
                        'landlord_id' => $landlord->user_id,
                        'tenant_id' => $tenant->user_id,
                        'room_id' => $unit->room_id,
                        'start_date' => Carbon::now()->subMonths(random_int(1, 8))->startOfMonth(),
                        'end_date' => Carbon::now()->addYear(),
                        'monthly_rent' => $rentAmount,
                        'security_deposit' => $rentAmount,
                        'status' => 'active',
                    ]);

                    $unit->update(['available' => false, 'status' => 'occupied']);
                }
            }
        }
    }
}
