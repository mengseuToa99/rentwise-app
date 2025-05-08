<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'setting_name' => 'site_name',
                'setting_value' => 'RentWise',
                'description' => 'The name of the application'
            ],
            [
                'setting_name' => 'invoice_due_days',
                'setting_value' => '15',
                'description' => 'Number of days before an invoice is due after generation'
            ],
            [
                'setting_name' => 'late_fee_percentage',
                'setting_value' => '5',
                'description' => 'Percentage of rent charged as late fee'
            ],
            [
                'setting_name' => 'maintenance_approval_required',
                'setting_value' => 'yes',
                'description' => 'Whether maintenance requests require landlord approval'
            ],
            [
                'setting_name' => 'default_lease_length',
                'setting_value' => '12',
                'description' => 'Default lease length in months'
            ],
            [
                'setting_name' => 'currency',
                'setting_value' => 'USD',
                'description' => 'Default currency for the application'
            ],
            [
                'setting_name' => 'enable_auto_invoicing',
                'setting_value' => 'yes',
                'description' => 'Automatically generate monthly invoices'
            ],
            [
                'setting_name' => 'payment_reminder_days',
                'setting_value' => '3',
                'description' => 'Days before due date to send payment reminder'
            ],
            [
                'setting_name' => 'company_email',
                'setting_value' => 'support@rentwise.com',
                'description' => 'Company support email address'
            ],
            [
                'setting_name' => 'company_phone',
                'setting_value' => '+1-123-456-7890',
                'description' => 'Company support phone number'
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['setting_name' => $setting['setting_name']],
                [
                    'setting_value' => $setting['setting_value'],
                    'description' => $setting['description']
                ]
            );
        }
    }
} 