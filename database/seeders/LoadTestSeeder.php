<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoadTestSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $totalUsers = max(1000, (int) env('LOAD_TEST_TOTAL_USERS', 10000));
        $landlordCount = max(10, (int) env('LOAD_TEST_LANDLORDS', 200));
        $landlordCount = min($landlordCount, $totalUsers - 2);
        $tenantCount = $totalUsers - $landlordCount - 1; // reserve one admin user

        $propertiesPerLandlord = max(1, (int) env('LOAD_TEST_PROPERTIES_PER_LANDLORD', 4));
        $unitsPerProperty = max(2, (int) env('LOAD_TEST_UNITS_PER_PROPERTY', 8));
        $invoiceMonths = max(1, (int) env('LOAD_TEST_INVOICE_MONTHS', 2));
        $passwordHash = Hash::make('password');

        $maintenanceRate = (float) env('LOAD_TEST_MAINTENANCE_RATE', 0.12);
        $maintenanceRate = max(0.0, min(1.0, $maintenanceRate));

        $this->command?->info('Preparing load-test dataset...');

        $this->cleanupPreviousLoadTestData();

        $roleIds = $this->ensureRoles($now);

        $adminId = $this->createLoadTestAdmin($roleIds['admin'], $passwordHash, $now);
        $landlordIds = $this->insertLandlords($landlordCount, $roleIds['landlord'], $passwordHash, $now);
        $tenantIds = $this->insertTenants($tenantCount, $roleIds['tenant'], $passwordHash, $now);

        $propertyIds = $this->insertProperties($landlordIds, $propertiesPerLandlord, $now);
        [$unitIds, $unitRentMap] = $this->insertUnits($propertyIds, $unitsPerProperty, $now);

        $activeRentalsTarget = min(count($tenantIds), count($unitIds));
        $rentals = $this->insertRentals($landlordIds, $tenantIds, $unitIds, $unitRentMap, $activeRentalsTarget, $now);

        $this->insertInvoices($rentals, $invoiceMonths, $now);
        $this->insertMaintenanceRequests($rentals, $maintenanceRate, $now);

        $this->command?->newLine();
        $this->command?->info('Load-test dataset created.');
        $this->command?->line("Admin user: lt_admin@example.com (user_id {$adminId})");
        $this->command?->line('Password for generated users: password');
        $this->command?->line("Users: {$totalUsers} | Landlords: {$landlordCount} | Tenants: {$tenantCount}");
        $this->command?->line('Tip: run php artisan optimize before running benchmarks.');
    }

    private function cleanupPreviousLoadTestData(): void
    {
        $ids = DB::table('users')
            ->where('username', 'like', 'lt_%')
            ->pluck('user_id');

        if ($ids->isEmpty()) {
            return;
        }

        // Hard-delete old load-test users. FKs cascade to related entities.
        DB::table('users')->whereIn('user_id', $ids)->delete();
    }

    private function ensureRoles(Carbon $now): array
    {
        $roles = [
            'admin' => 'System Administrator',
            'landlord' => 'Property Owner',
            'tenant' => 'Property Renter',
        ];

        foreach ($roles as $name => $description) {
            $exists = DB::table('roles')->where('role_name', $name)->exists();
            if (!$exists) {
                DB::table('roles')->insert([
                    'role_name' => $name,
                    'description' => $description,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        return DB::table('roles')
            ->whereIn('role_name', array_keys($roles))
            ->pluck('role_id', 'role_name')
            ->all();
    }

    private function createLoadTestAdmin(int $adminRoleId, string $passwordHash, Carbon $now): int
    {
        $admin = DB::table('users')->where('username', 'lt_admin')->first();

        if ($admin) {
            $adminId = (int) $admin->user_id;
        } else {
            $adminId = (int) DB::table('users')->insertGetId([
                'username' => 'lt_admin',
                'email' => 'lt_admin@example.com',
                'password_hash' => $passwordHash,
                'phone_number' => '+10000000000',
                'status' => 'active',
                'first_name' => 'Load',
                'last_name' => 'Admin',
                'created_at' => $now,
                'updated_at' => $now,
            ], 'user_id');
        }

        DB::table('user_roles')->updateOrInsert(
            ['user_id' => $adminId, 'role_id' => $adminRoleId],
            ['created_at' => $now, 'updated_at' => $now]
        );

        return $adminId;
    }

    private function insertLandlords(int $count, int $landlordRoleId, string $passwordHash, Carbon $now): array
    {
        $this->command?->line("Creating {$count} landlords...");

        $landlordIds = [];
        $chunkSize = 500;

        for ($start = 1; $start <= $count; $start += $chunkSize) {
            $end = min($count, $start + $chunkSize - 1);

            $users = [];
            for ($i = $start; $i <= $end; $i++) {
                $users[] = [
                    'username' => sprintf('lt_landlord_%05d', $i),
                    'email' => sprintf('lt_landlord_%05d@example.com', $i),
                    'password_hash' => $passwordHash,
                    'phone_number' => sprintf('+1200%07d', $i),
                    'status' => 'active',
                    'first_name' => 'Landlord',
                    'last_name' => (string) $i,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('users')->insert($users);

            $inserted = DB::table('users')
                ->whereBetween('username', [
                    sprintf('lt_landlord_%05d', $start),
                    sprintf('lt_landlord_%05d', $end),
                ])
                ->pluck('user_id')
                ->all();

            $landlordIds = array_merge($landlordIds, $inserted);

            $roleRows = [];
            foreach ($inserted as $userId) {
                $roleRows[] = [
                    'user_id' => $userId,
                    'role_id' => $landlordRoleId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('user_roles')->insert($roleRows);
        }

        return $landlordIds;
    }

    private function insertTenants(int $count, int $tenantRoleId, string $passwordHash, Carbon $now): array
    {
        $this->command?->line("Creating {$count} tenants...");

        $tenantIds = [];
        $chunkSize = 500;

        for ($start = 1; $start <= $count; $start += $chunkSize) {
            $end = min($count, $start + $chunkSize - 1);

            $users = [];
            for ($i = $start; $i <= $end; $i++) {
                $users[] = [
                    'username' => sprintf('lt_tenant_%05d', $i),
                    'email' => sprintf('lt_tenant_%05d@example.com', $i),
                    'password_hash' => $passwordHash,
                    'phone_number' => sprintf('+1300%07d', $i),
                    'status' => 'active',
                    'first_name' => 'Tenant',
                    'last_name' => (string) $i,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('users')->insert($users);

            $inserted = DB::table('users')
                ->whereBetween('username', [
                    sprintf('lt_tenant_%05d', $start),
                    sprintf('lt_tenant_%05d', $end),
                ])
                ->pluck('user_id')
                ->all();

            $tenantIds = array_merge($tenantIds, $inserted);

            $roleRows = [];
            foreach ($inserted as $userId) {
                $roleRows[] = [
                    'user_id' => $userId,
                    'role_id' => $tenantRoleId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('user_roles')->insert($roleRows);
        }

        return $tenantIds;
    }

    private function insertProperties(array $landlordIds, int $perLandlord, Carbon $now): array
    {
        $this->command?->line('Creating properties...');

        $rows = [];
        foreach ($landlordIds as $landlordId) {
            for ($i = 1; $i <= $perLandlord; $i++) {
                $rows[] = [
                    'landlord_id' => $landlordId,
                    'property_name' => "LT Property {$landlordId}-{$i}",
                    'house_building_number' => (string) rand(1, 999),
                    'street' => 'Benchmark Street',
                    'village' => 'Benchmark Village',
                    'commune' => 'Benchmark Commune',
                    'district' => 'Benchmark District',
                    'province' => 'Phnom Penh',
                    'total_floors' => rand(2, 8),
                    'total_rooms' => 0,
                    'description' => 'Auto-generated load testing property',
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('property_details')->insert($chunk);
        }

        return DB::table('property_details')
            ->whereIn('landlord_id', $landlordIds)
            ->where('property_name', 'like', 'LT Property %')
            ->pluck('property_id')
            ->all();
    }

    private function insertUnits(array $propertyIds, int $perProperty, Carbon $now): array
    {
        $this->command?->line('Creating units...');

        $rows = [];
        foreach ($propertyIds as $propertyId) {
            for ($i = 1; $i <= $perProperty; $i++) {
                $rent = rand(180, 950);
                $rows[] = [
                    'property_id' => $propertyId,
                    'room_number' => sprintf('R%03d', $i),
                    'room_name' => "Unit {$i}",
                    'floor_number' => max(1, (int) ceil($i / 4)),
                    'type' => 'apartment',
                    'room_type' => $i % 3 === 0 ? 'two_bedroom' : 'one_bedroom',
                    'description' => 'Auto-generated load testing unit',
                    'available' => true,
                    'status' => 'vacant',
                    'rent_amount' => $rent,
                    'due_date' => now()->addDays(rand(1, 28))->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('room_details')->insert($chunk);
        }

        $units = DB::table('room_details')
            ->whereIn('property_id', $propertyIds)
            ->where('room_name', 'like', 'Unit %')
            ->select(['room_id', 'rent_amount'])
            ->get();

        $unitIds = $units->pluck('room_id')->all();
        $rentMap = [];
        foreach ($units as $unit) {
            $rentMap[(int) $unit->room_id] = (float) $unit->rent_amount;
        }

        // Keep property totals roughly aligned for dashboard display.
        DB::table('property_details')
            ->whereIn('property_id', $propertyIds)
            ->update(['total_rooms' => $perProperty, 'updated_at' => $now]);

        return [$unitIds, $rentMap];
    }

    private function insertRentals(
        array $landlordIds,
        array $tenantIds,
        array $unitIds,
        array $unitRentMap,
        int $target,
        Carbon $now
    ): array {
        $this->command?->line("Creating {$target} active rentals...");

        $propertyLandlordMap = DB::table('room_details')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->whereIn('room_details.room_id', array_slice($unitIds, 0, $target))
            ->pluck('property_details.landlord_id', 'room_details.room_id')
            ->all();

        $rows = [];
        for ($i = 0; $i < $target; $i++) {
            $roomId = (int) $unitIds[$i];
            $startDate = Carbon::now()->subMonths(rand(1, 10))->startOfMonth();
            $endDate = (clone $startDate)->addYear();

            $rows[] = [
                'landlord_id' => (int) ($propertyLandlordMap[$roomId] ?? $landlordIds[array_rand($landlordIds)]),
                'tenant_id' => (int) $tenantIds[$i],
                'room_id' => $roomId,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'monthly_rent' => $unitRentMap[$roomId] ?? rand(180, 950),
                'security_deposit' => ($unitRentMap[$roomId] ?? 500) * 1.5,
                'signed_by_tenant' => true,
                'signed_by_landlord' => true,
                'signed_at' => $startDate,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('rental_details')->insert($chunk);
        }

        DB::table('room_details')
            ->whereIn('room_id', array_slice($unitIds, 0, $target))
            ->update(['available' => false, 'status' => 'occupied', 'updated_at' => $now]);

        return DB::table('rental_details')
            ->whereIn('tenant_id', array_slice($tenantIds, 0, $target))
            ->where('status', 'active')
            ->select(['rental_id', 'landlord_id', 'tenant_id', 'room_id', 'start_date', 'monthly_rent'])
            ->get()
            ->all();
    }

    private function insertInvoices(array $rentals, int $months, Carbon $now): void
    {
        $this->command?->line('Creating invoices...');

        $rows = [];
        foreach ($rentals as $rental) {
            $rent = (float) ($rental->monthly_rent ?: rand(180, 950));

            for ($i = 0; $i < $months; $i++) {
                $periodStart = Carbon::now()->subMonths($i)->startOfMonth();
                $periodEnd = (clone $periodStart)->endOfMonth();
                $dueDate = (clone $periodStart)->addDays(9);
                $isPaid = $i > 0 || rand(0, 100) < 70;
                $amountPaid = $isPaid ? $rent : 0;
                $status = $isPaid
                    ? 'paid'
                    : (Carbon::now()->gt($dueDate) ? 'overdue' : 'pending');

                $rows[] = [
                    'rental_id' => $rental->rental_id,
                    'invoice_number' => sprintf('LT-%d-%s', $rental->rental_id, $periodStart->format('Ym')),
                    'amount_due' => $rent,
                    'amount_paid' => $amountPaid,
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'issue_date' => $periodStart->toDateString(),
                    'due_date' => $dueDate->toDateString(),
                    'payment_status' => $status,
                    'notes' => 'Auto-generated for load testing',
                    'created_at' => $now,
                    'updated_at' => $isPaid ? $periodEnd : $now,
                ];
            }
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('invoice_details')->insert($chunk);
        }
    }

    private function insertMaintenanceRequests(array $rentals, float $rate, Carbon $now): void
    {
        $target = (int) floor(count($rentals) * $rate);
        if ($target < 1) {
            return;
        }

        $this->command?->line("Creating {$target} maintenance requests...");

        $roomToProperty = DB::table('room_details')
            ->whereIn('room_id', collect($rentals)->pluck('room_id')->all())
            ->pluck('property_id', 'room_id')
            ->all();

        $statuses = ['pending', 'in_progress', 'completed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $titles = ['Air conditioner issue', 'Water leakage', 'Electrical outlet fault', 'Door lock problem'];

        $rows = [];
        for ($i = 0; $i < $target; $i++) {
            $rental = $rentals[$i];
            $status = $statuses[array_rand($statuses)];

            $rows[] = [
                'tenant_id' => $rental->tenant_id,
                'landlord_id' => $rental->landlord_id,
                'property_id' => $roomToProperty[$rental->room_id] ?? null,
                'room_id' => $rental->room_id,
                'rental_id' => $rental->rental_id,
                'title' => $titles[array_rand($titles)],
                'description' => 'Auto-generated maintenance request for load testing.',
                'priority' => $priorities[array_rand($priorities)],
                'status' => $status,
                'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 15)) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('maintenance_requests')->insert($chunk);
        }
    }
}
