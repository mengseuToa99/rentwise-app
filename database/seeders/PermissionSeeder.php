<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        // Create roles
        $adminRole = DB::table('roles')->updateOrInsert(
            ['role_name' => 'Admin'],
            [
                'description' => 'System administrator with full access to all features',
                'created_at' => $now,
                'updated_at' => $now,
                'parent_role_id' => null
            ]
        );
        $adminRoleId = DB::table('roles')->where('role_name', 'Admin')->value('role_id');

        $landlordRole = DB::table('roles')->updateOrInsert(
            ['role_name' => 'Landlord'],
            [
                'description' => 'Property owner with management capabilities',
                'created_at' => $now,
                'updated_at' => $now,
                'parent_role_id' => null
            ]
        );
        $landlordRoleId = DB::table('roles')->where('role_name', 'Landlord')->value('role_id');

        $tenantRole = DB::table('roles')->updateOrInsert(
            ['role_name' => 'Tenant'],
            [
                'description' => 'Property renter with limited access',
                'created_at' => $now,
                'updated_at' => $now,
                'parent_role_id' => null
            ]
        );
        $tenantRoleId = DB::table('roles')->where('role_name', 'Tenant')->value('role_id');

        // Create permission groups
        $permissionGroups = [
            [
                'name' => 'User Management',
                'description' => 'Permissions related to user account management'
            ],
            [
                'name' => 'System Configuration',
                'description' => 'Permissions related to system settings and configuration'
            ],
            [
                'name' => 'Integration Management',
                'description' => 'Permissions related to third-party integrations'
            ],
            [
                'name' => 'Property Management',
                'description' => 'Permissions related to property and room management'
            ],
            [
                'name' => 'Rental Management',
                'description' => 'Permissions related to rental agreements and leases'
            ],
            [
                'name' => 'Financial Management',
                'description' => 'Permissions related to invoices, payments and financials'
            ],
            [
                'name' => 'Maintenance Management',
                'description' => 'Permissions related to maintenance requests'
            ],
            [
                'name' => 'Communication',
                'description' => 'Permissions related to messaging and notifications'
            ],
            [
                'name' => 'Document Management',
                'description' => 'Permissions related to document storage and sharing'
            ],
            [
                'name' => 'Reporting',
                'description' => 'Permissions related to reports and analytics'
            ]
        ];

        $groupIdMap = [];
        
        foreach ($permissionGroups as $group) {
            // Check if the group already exists
            $existingGroup = DB::table('permission_groups')->where('group_name', $group['name'])->first();
            
            if ($existingGroup) {
                $groupIdMap[$group['name']] = $existingGroup->group_id;
            } else {
                // Create a new group if it doesn't exist
                DB::table('permission_groups')->insert([
                    'group_name' => $group['name'],
                    'description' => $group['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $groupIdMap[$group['name']] = DB::table('permission_groups')
                    ->where('group_name', $group['name'])
                    ->value('group_id');
            }
        }

        // Define all permissions with their groups
        $allPermissions = [
            // User Management
            [
                'permission_name' => 'create_user',
                'description' => 'Create new user accounts',
                'group_id' => $groupIdMap['User Management']
            ],
            [
                'permission_name' => 'view_user',
                'description' => 'View user account details',
                'group_id' => $groupIdMap['User Management']
            ],
            [
                'permission_name' => 'edit_user',
                'description' => 'Edit user account details',
                'group_id' => $groupIdMap['User Management']
            ],
            [
                'permission_name' => 'delete_user',
                'description' => 'Delete user accounts',
                'group_id' => $groupIdMap['User Management']
            ],
            [
                'permission_name' => 'assign_roles',
                'description' => 'Assign roles to users',
                'group_id' => $groupIdMap['User Management']
            ],
            [
                'permission_name' => 'manage_verification',
                'description' => 'Manage user verification processes',
                'group_id' => $groupIdMap['User Management']
            ],
            [
                'permission_name' => 'view_audit_logs',
                'description' => 'View system audit logs',
                'group_id' => $groupIdMap['User Management']
            ],
            [
                'permission_name' => 'edit_own_profile',
                'description' => 'Edit own user profile',
                'group_id' => $groupIdMap['User Management']
            ],
            
            // System Configuration
            [
                'permission_name' => 'manage_system_settings',
                'description' => 'Configure system settings',
                'group_id' => $groupIdMap['System Configuration']
            ],
            [
                'permission_name' => 'manage_rent_rules',
                'description' => 'Configure rent calculation rules',
                'group_id' => $groupIdMap['System Configuration']
            ],
            [
                'permission_name' => 'manage_notification_settings',
                'description' => 'Configure system notifications',
                'group_id' => $groupIdMap['System Configuration']
            ],
            [
                'permission_name' => 'view_system_logs',
                'description' => 'View system logs',
                'group_id' => $groupIdMap['System Configuration']
            ],
            [
                'permission_name' => 'view_admin_dashboard',
                'description' => 'View admin dashboard and analytics',
                'group_id' => $groupIdMap['System Configuration']
            ],
            
            // Integration Management
            [
                'permission_name' => 'manage_integrations',
                'description' => 'Set up and configure third-party integrations',
                'group_id' => $groupIdMap['Integration Management']
            ],
            [
                'permission_name' => 'view_integration_logs',
                'description' => 'View integration activity logs',
                'group_id' => $groupIdMap['Integration Management']
            ],
            
            // Property Management
            [
                'permission_name' => 'create_property',
                'description' => 'Add new properties',
                'group_id' => $groupIdMap['Property Management']
            ],
            [
                'permission_name' => 'view_property',
                'description' => 'View property details',
                'group_id' => $groupIdMap['Property Management']
            ],
            [
                'permission_name' => 'edit_property',
                'description' => 'Edit property details',
                'group_id' => $groupIdMap['Property Management']
            ],
            [
                'permission_name' => 'delete_property',
                'description' => 'Delete properties',
                'group_id' => $groupIdMap['Property Management']
            ],
            [
                'permission_name' => 'manage_property_images',
                'description' => 'Add or remove property images',
                'group_id' => $groupIdMap['Property Management']
            ],
            [
                'permission_name' => 'create_room',
                'description' => 'Add new rooms to properties',
                'group_id' => $groupIdMap['Property Management']
            ],
            [
                'permission_name' => 'view_room',
                'description' => 'View room details',
                'group_id' => $groupIdMap['Property Management']
            ],
            [
                'permission_name' => 'edit_room',
                'description' => 'Edit room details',
                'group_id' => $groupIdMap['Property Management']
            ],
            [
                'permission_name' => 'delete_room',
                'description' => 'Delete rooms',
                'group_id' => $groupIdMap['Property Management']
            ],
            
            // Rental Management
            [
                'permission_name' => 'create_rental',
                'description' => 'Create new rental agreements',
                'group_id' => $groupIdMap['Rental Management']
            ],
            [
                'permission_name' => 'view_rental',
                'description' => 'View rental details',
                'group_id' => $groupIdMap['Rental Management']
            ],
            [
                'permission_name' => 'edit_rental',
                'description' => 'Edit rental details',
                'group_id' => $groupIdMap['Rental Management']
            ],
            [
                'permission_name' => 'terminate_rental',
                'description' => 'Terminate rental agreements',
                'group_id' => $groupIdMap['Rental Management']
            ],
            [
                'permission_name' => 'view_lease_agreements',
                'description' => 'View lease agreement documents',
                'group_id' => $groupIdMap['Rental Management']
            ],
            
            // Financial Management
            [
                'permission_name' => 'create_invoice',
                'description' => 'Generate new invoices',
                'group_id' => $groupIdMap['Financial Management']
            ],
            [
                'permission_name' => 'view_invoice',
                'description' => 'View invoice details',
                'group_id' => $groupIdMap['Financial Management']
            ],
            [
                'permission_name' => 'edit_invoice',
                'description' => 'Edit invoice details',
                'group_id' => $groupIdMap['Financial Management']
            ],
            [
                'permission_name' => 'delete_invoice',
                'description' => 'Delete invoices',
                'group_id' => $groupIdMap['Financial Management']
            ],
            [
                'permission_name' => 'record_payment',
                'description' => 'Record rent payments',
                'group_id' => $groupIdMap['Financial Management']
            ],
            [
                'permission_name' => 'view_payment_history',
                'description' => 'View payment history',
                'group_id' => $groupIdMap['Financial Management']
            ],
            [
                'permission_name' => 'manage_utility_prices',
                'description' => 'Set and manage utility prices',
                'group_id' => $groupIdMap['Financial Management']
            ],
            [
                'permission_name' => 'record_utility_usage',
                'description' => 'Record utility meter readings',
                'group_id' => $groupIdMap['Financial Management']
            ],
            
            // Maintenance Management
            [
                'permission_name' => 'create_maintenance_request',
                'description' => 'Create maintenance requests',
                'group_id' => $groupIdMap['Maintenance Management']
            ],
            [
                'permission_name' => 'view_maintenance_request',
                'description' => 'View maintenance request details',
                'group_id' => $groupIdMap['Maintenance Management']
            ],
            [
                'permission_name' => 'update_maintenance_request',
                'description' => 'Update maintenance request status',
                'group_id' => $groupIdMap['Maintenance Management']
            ],
            [
                'permission_name' => 'delete_maintenance_request',
                'description' => 'Delete maintenance requests',
                'group_id' => $groupIdMap['Maintenance Management']
            ],
            
            // Communication
            [
                'permission_name' => 'create_conversation',
                'description' => 'Start new conversations',
                'group_id' => $groupIdMap['Communication']
            ],
            [
                'permission_name' => 'view_conversation',
                'description' => 'View conversation details',
                'group_id' => $groupIdMap['Communication']
            ],
            [
                'permission_name' => 'reply_to_conversation',
                'description' => 'Reply to conversations',
                'group_id' => $groupIdMap['Communication']
            ],
            [
                'permission_name' => 'delete_conversation',
                'description' => 'Delete conversations',
                'group_id' => $groupIdMap['Communication']
            ],
            
            // Document Management
            [
                'permission_name' => 'upload_document',
                'description' => 'Upload documents to the system',
                'group_id' => $groupIdMap['Document Management']
            ],
            [
                'permission_name' => 'view_document',
                'description' => 'View documents',
                'group_id' => $groupIdMap['Document Management']
            ],
            [
                'permission_name' => 'share_document',
                'description' => 'Share documents with others',
                'group_id' => $groupIdMap['Document Management']
            ],
            [
                'permission_name' => 'delete_document',
                'description' => 'Delete documents',
                'group_id' => $groupIdMap['Document Management']
            ],
            
            // Reporting
            [
                'permission_name' => 'view_financial_reports',
                'description' => 'View financial reports and analytics',
                'group_id' => $groupIdMap['Reporting']
            ],
            [
                'permission_name' => 'view_property_reports',
                'description' => 'View property performance reports',
                'group_id' => $groupIdMap['Reporting']
            ],
            [
                'permission_name' => 'view_tenant_reports',
                'description' => 'View tenant-related reports',
                'group_id' => $groupIdMap['Reporting']
            ],
            [
                'permission_name' => 'export_reports',
                'description' => 'Export reports to various formats',
                'group_id' => $groupIdMap['Reporting']
            ]
        ];

        // Define role permissions
        $rolePermissions = [
            // Admin has all permissions
            $adminRoleId => array_column($allPermissions, 'permission_name'),
            
            // Landlord permissions
            $landlordRoleId => [
                'view_user',
                'edit_own_profile',
                'create_property',
                'view_property',
                'edit_property',
                'delete_property',
                'manage_property_images',
                'create_room',
                'view_room',
                'edit_room',
                'delete_room',
                'create_rental',
                'view_rental',
                'edit_rental',
                'terminate_rental',
                'view_lease_agreements',
                'create_invoice',
                'view_invoice',
                'edit_invoice',
                'delete_invoice',
                'record_payment',
                'view_payment_history',
                'manage_utility_prices',
                'record_utility_usage',
                'view_maintenance_request',
                'update_maintenance_request',
                'create_conversation',
                'view_conversation',
                'reply_to_conversation',
                'delete_conversation',
                'upload_document',
                'view_document',
                'share_document',
                'delete_document',
                'view_financial_reports',
                'view_property_reports',
                'view_tenant_reports',
                'export_reports'
            ],
            
            // Tenant permissions
            $tenantRoleId => [
                'edit_own_profile',
                'view_property',
                'view_room',
                'view_rental',
                'view_lease_agreements',
                'view_invoice',
                'view_payment_history',
                'create_maintenance_request',
                'view_maintenance_request',
                'create_conversation',
                'view_conversation',
                'reply_to_conversation',
                'view_document'
            ]
        ];
        
        // Create a mapping to track which permissions have been assigned to which roles
        $assignedPermissions = [];
        
        // Insert permissions for each role
        foreach ($rolePermissions as $roleId => $permissions) {
            foreach ($permissions as $permissionName) {
                // Find the permission details in the allPermissions array
                $permissionData = null;
                foreach ($allPermissions as $perm) {
                    if ($perm['permission_name'] === $permissionName) {
                        $permissionData = $perm;
                        break;
                    }
                }
                
                if ($permissionData) {
                    // Insert the permission
                    DB::table('access_permissions')->insert([
                        'permission_name' => $permissionName,
                        'description' => $permissionData['description'],
                        'group_id' => $permissionData['group_id'],
                        'role_id' => $roleId,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }
            }
        }

        // Create a test user for each role
        // Check if admin user exists
        $admin = DB::table('users')->where('username', 'admin')->first();
        if (!$admin) {
            DB::table('users')->insert([
                'username' => 'admin',
                'password_hash' => Hash::make('password'),
                'email' => 'admin@example.com',
                'phone_number' => '123-456-7890',
                'status' => 'active',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'created_at' => $now,
                'updated_at' => $now
            ]);
            $adminId = DB::table('users')->where('username', 'admin')->value('user_id');
        } else {
            $adminId = $admin->user_id;
        }

        // Check if landlord user exists
        $landlord = DB::table('users')->where('username', 'landlord')->first();
        if (!$landlord) {
            DB::table('users')->insert([
                'username' => 'landlord',
                'password_hash' => Hash::make('password'),
                'email' => 'landlord@example.com',
                'phone_number' => '123-456-7891',
                'status' => 'active',
                'first_name' => 'Property',
                'last_name' => 'Owner',
                'created_at' => $now,
                'updated_at' => $now
            ]);
            $landlordId = DB::table('users')->where('username', 'landlord')->value('user_id');
        } else {
            $landlordId = $landlord->user_id;
        }

        // Check if tenant user exists
        $tenant = DB::table('users')->where('username', 'tenant')->first();
        if (!$tenant) {
            DB::table('users')->insert([
                'username' => 'tenant',
                'password_hash' => Hash::make('password'),
                'email' => 'tenant@example.com',
                'phone_number' => '123-456-7892',
                'status' => 'active',
                'first_name' => 'Rental',
                'last_name' => 'User',
                'created_at' => $now,
                'updated_at' => $now
            ]);
            $tenantId = DB::table('users')->where('username', 'tenant')->value('user_id');
        } else {
            $tenantId = $tenant->user_id;
        }

        // Assign roles to users
        // Check if roles are already assigned
        $adminRoleAssigned = DB::table('user_roles')
            ->where('user_id', $adminId)
            ->where('role_id', $adminRoleId)
            ->exists();
            
        $landlordRoleAssigned = DB::table('user_roles')
            ->where('user_id', $landlordId)
            ->where('role_id', $landlordRoleId)
            ->exists();
            
        $tenantRoleAssigned = DB::table('user_roles')
            ->where('user_id', $tenantId)
            ->where('role_id', $tenantRoleId)
            ->exists();

        // Insert missing role assignments
        $roleAssignments = [];
        
        if (!$adminRoleAssigned) {
            $roleAssignments[] = [
                'user_id' => $adminId,
                'role_id' => $adminRoleId,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        
        if (!$landlordRoleAssigned) {
            $roleAssignments[] = [
                'user_id' => $landlordId,
                'role_id' => $landlordRoleId,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        
        if (!$tenantRoleAssigned) {
            $roleAssignments[] = [
                'user_id' => $tenantId,
                'role_id' => $tenantRoleId,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        
        // Only insert if there are new role assignments
        if (count($roleAssignments) > 0) {
            DB::table('user_roles')->insert($roleAssignments);
        }
    }
}