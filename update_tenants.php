<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Update tenants with real names
$tenantData = [
    [
        'username' => 'tenant1',
        'first_name' => 'David',
        'last_name' => 'Chen',
        'phone_number' => '+855 10 123 456',
    ],
    [
        'username' => 'tenant2',
        'first_name' => 'Lisa',
        'last_name' => 'Nguyen',
        'phone_number' => '+855 10 234 567',
    ],
    [
        'username' => 'tenant3',
        'first_name' => 'James',
        'last_name' => 'Kim',
        'phone_number' => '+855 10 345 678',
    ],
    [
        'username' => 'tenant4',
        'first_name' => 'Maria',
        'last_name' => 'Garcia',
        'phone_number' => '+855 10 456 789',
    ],
    [
        'username' => 'tenant5',
        'first_name' => 'Ahmed',
        'last_name' => 'Hassan',
        'phone_number' => '+855 10 567 890',
    ],
    [
        'username' => 'tenant6',
        'first_name' => 'Emma',
        'last_name' => 'Wilson',
        'phone_number' => '+855 10 678 901',
    ],
    [
        'username' => 'tenant7',
        'first_name' => 'Carlos',
        'last_name' => 'Rodriguez',
        'phone_number' => '+855 10 789 012',
    ],
    [
        'username' => 'tenant8',
        'first_name' => 'Priya',
        'last_name' => 'Patel',
        'phone_number' => '+855 10 890 123',
    ],
    [
        'username' => 'tenant9',
        'first_name' => 'Sothea',
        'last_name' => 'Khmer',
        'phone_number' => '+855 10 901 234',
    ],
    [
        'username' => 'tenant10',
        'first_name' => 'Makara',
        'last_name' => 'Sok',
        'phone_number' => '+855 10 012 345',
    ],
];

// Also update the default tenant
DB::table('users')
    ->where('username', 'tenant')
    ->update([
        'first_name' => 'Default',
        'last_name' => 'Tenant',
        'email' => 'default.tenant@example.com',
        'phone_number' => '+855 11 222 333',
    ]);

echo "Updated default tenant\n";

foreach ($tenantData as $data) {
    $updated = DB::table('users')
        ->where('username', $data['username'])
        ->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],
            'email' => strtolower($data['first_name']) . '.' . strtolower($data['last_name']) . '@example.com',
        ]);
    
    if ($updated) {
        echo "Updated tenant {$data['username']}: {$data['first_name']} {$data['last_name']}\n";
    } else {
        echo "Could not find tenant {$data['username']}\n";
    }
}

echo "All tenants updated successfully!\n"; 