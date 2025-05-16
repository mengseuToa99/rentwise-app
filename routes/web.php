<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard;
use App\Livewire\Properties\PropertyList;
use App\Livewire\Properties\PropertyCreate;
use App\Livewire\Properties\PropertyEdit;
use App\Livewire\Properties\PropertyDetail;
use App\Livewire\Units\UnitList;
use App\Livewire\Units\UnitCreate;
use App\Livewire\Units\UnitEdit;
// New components
use App\Livewire\Rentals\RentalList;
use App\Livewire\Rentals\RentalForm;
use App\Livewire\Invoices\InvoiceList;
use App\Livewire\Invoices\InvoiceForm;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
// Commented out until implemented
/*
use App\Livewire\Users\UserList;
use App\Livewire\Users\UserCreate;
use App\Livewire\Users\UserEdit;
use App\Livewire\Messages\MessageList;
use App\Livewire\Messages\MessageDetail;
use App\Livewire\Admin\Roles\RoleList;
use App\Livewire\Admin\Roles\RoleCreate;
use App\Livewire\Admin\Roles\RoleEdit;
use App\Livewire\Admin\Permissions\PermissionList;
use App\Livewire\Admin\Permissions\PermissionCreate;
use App\Livewire\Admin\Permissions\PermissionEdit;
use App\Livewire\Admin\PermissionGroups\PermissionGroupList;
use App\Livewire\Admin\PermissionGroups\PermissionGroupCreate;
use App\Livewire\Admin\PermissionGroups\PermissionGroupEdit;
*/
use App\Livewire\PricingGroups\PricingGroupList;
use App\Livewire\PricingGroups\PricingGroupCreate;
use App\Livewire\PricingGroups\PricingGroupEdit;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Social Authentication Routes
Route::get('/auth/redirect/{provider}', [\App\Http\Controllers\SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/callback/{provider}', [\App\Http\Controllers\SocialAuthController::class, 'callback'])->name('social.callback');

// Phone Authentication Route
Route::get('/auth/phone', \App\Livewire\Auth\PhoneVerification::class)->name('phone.verification');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard - accessible to all authenticated users
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Profile - accessible to all authenticated users
    Route::get('/profile', \App\Livewire\Profile::class)->name('profile');
    
    // Settings routes
    Route::get('/settings/profile', Profile::class)->name('settings.profile');
    Route::get('/settings/password', Password::class)->name('settings.password');
    Route::get('/settings/appearance', Appearance::class)->name('settings.appearance');

    // Routes for landlords only (previously for both landlords and admins)
    Route::middleware([\App\Http\Middleware\CheckRole::class.':landlord'])->group(function () {
        // Properties
        Route::get('/properties', PropertyList::class)->name('properties.index');
        Route::get('/properties/create', PropertyCreate::class)->name('properties.create');
        Route::get('/properties/{property}/edit', PropertyEdit::class)->name('properties.edit');
        Route::get('/properties/{property}', PropertyDetail::class)->name('properties.show');
        
        // Units
        Route::get('/units', UnitList::class)->name('units.index');
        Route::get('/units/create', UnitCreate::class)->name('units.create');
        Route::get('/units/{unit}/edit', UnitEdit::class)->name('units.edit');
        
        // Pricing Groups
        Route::get('/properties/{property}/pricing-groups', PricingGroupList::class)->name('pricing-groups.index');
        Route::get('/properties/{property}/pricing-groups/create', PricingGroupCreate::class)->name('pricing-groups.create');
        Route::get('/properties/{property}/pricing-groups/{group}/edit', PricingGroupEdit::class)->name('pricing-groups.edit');
        
        // Rentals
        Route::get('/rentals', RentalList::class)->name('rentals.index');
        Route::get('/rentals/create', RentalForm::class)->name('rentals.create');
        Route::get('/rentals/{rentalId}/edit', RentalForm::class)->name('rentals.edit');
        
        // Invoices management
        Route::get('/invoices', InvoiceList::class)->name('invoices.index');
        Route::get('/invoices/create', InvoiceForm::class)->name('invoices.create');
        Route::get('/invoices/{invoiceId}/edit', InvoiceForm::class)->name('invoices.edit');
        Route::get('/invoices/{invoiceId}/view', \App\Livewire\Invoices\InvoiceDisplay::class)->name('invoices.view');
        
        // Landlord-specific named routes (for dashboard links)
        Route::get('/landlord/properties', PropertyList::class)->name('landlord.properties');
        Route::get('/landlord/invoices', InvoiceList::class)->name('landlord.invoices');
    });
    
    // Tenant-only routes
    Route::middleware([\App\Http\Middleware\CheckRole::class.':tenant'])->group(function () {
        // Tenants can only view their invoices
        Route::get('/tenant/invoices', \App\Livewire\Invoices\InvoiceList::class)->name('tenant.invoices');
        Route::get('/tenant/invoices/{invoiceId}', \App\Livewire\Invoices\InvoiceDisplay::class)->name('tenant.invoice.view');
    });
    
    // Admin-only routes
    Route::middleware([\App\Http\Middleware\CheckRole::class.':admin'])->prefix('admin')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)
            ->middleware([\App\Http\Middleware\CheckPermission::class.':view_admin_dashboard'])
            ->name('admin.dashboard');
        
        // User Management
        Route::get('/users', \App\Livewire\Admin\UserManagement::class)->name('admin.users');
        
        // Role Management
        Route::get('/roles', \App\Livewire\Admin\RoleManagement::class)->name('admin.roles');
        
        // Permission Management
        Route::get('/permissions', \App\Livewire\Admin\PermissionManagement::class)->name('admin.permissions');
        
        // System Settings Management
        Route::get('/settings', \App\Livewire\Admin\SystemSettings::class)
            ->middleware([\App\Http\Middleware\CheckPermission::class.':manage_system_settings'])
            ->name('admin.settings');
        
        // System Logs
        Route::get('/logs', \App\Livewire\Admin\SystemLogs::class)
            ->middleware([\App\Http\Middleware\CheckPermission::class.':view_system_logs'])
            ->name('admin.logs');
    });

    Route::get('/chat', \App\Livewire\Chat\ChatInterface::class)->name('chat');
});

// Test and diagnostic routes
Route::get('/test-role', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $roles = $user->roles ? $user->roles->pluck('role_name')->toArray() : [];
        return response()->json([
            'user_id' => $user->user_id,
            'email' => $user->email,
            'has_roles_relationship' => $user->roles !== null,
            'roles' => $roles
        ]);
    }
    return response()->json(['error' => 'Not authenticated']);
})->middleware('auth');

// Test route for admin role
Route::get('/test-admin', function () {
    return response()->json(['success' => true, 'message' => 'You are an admin!']);
})->middleware(['auth', \App\Http\Middleware\CheckRole::class.':admin']);

// Test route to create admin user
Route::get('/create-admin', function () {
    try {
        // Create or get admin user
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('password'),
                'phone_number' => '123-456-7890',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'status' => 'active',
            ]
        );
        
        // Get admin role
        $adminRole = \App\Models\Role::where('role_name', 'admin')->first();
        
        if (!$adminRole) {
            // Create admin role if it doesn't exist
            $adminRole = \App\Models\Role::create([
                'role_name' => 'admin',
                'description' => 'Administrator with full system access',
            ]);
        }
        
        // Create user role relationship in the pivot table directly
        \Illuminate\Support\Facades\DB::table('user_roles')->insert([
            'user_id' => $user->user_id,
            'role_id' => $adminRole->role_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Admin user created',
            'user' => $user->toArray(),
            'role' => $adminRole->toArray()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Create landlord test user
Route::get('/create-landlord', function () {
    try {
        // Create or get landlord user
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'landlord@example.com'],
            [
                'username' => 'landlord',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('password'),
                'phone_number' => '123-456-7891',
                'first_name' => 'Landlord',
                'last_name' => 'User',
                'status' => 'active',
            ]
        );
        
        // Get landlord role
        $landlordRole = \App\Models\Role::where('role_name', 'landlord')->first();
        
        if (!$landlordRole) {
            // Create landlord role if it doesn't exist
            $landlordRole = \App\Models\Role::create([
                'role_name' => 'landlord',
                'description' => 'Property owner with management access',
            ]);
        }
        
        // Create user role relationship in the pivot table directly
        \Illuminate\Support\Facades\DB::table('user_roles')->insert([
            'user_id' => $user->user_id,
            'role_id' => $landlordRole->role_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Landlord user created',
            'user' => $user->toArray(),
            'role' => $landlordRole->toArray()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Create tenant test user
Route::get('/create-tenant', function () {
    try {
        // Create or get tenant user
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'tenant@example.com'],
            [
                'username' => 'tenant',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('password'),
                'phone_number' => '123-456-7892',
                'first_name' => 'Tenant',
                'last_name' => 'User',
                'status' => 'active',
            ]
        );
        
        // Get tenant role
        $tenantRole = \App\Models\Role::where('role_name', 'tenant')->first();
        
        if (!$tenantRole) {
            // Create tenant role if it doesn't exist
            $tenantRole = \App\Models\Role::create([
                'role_name' => 'tenant',
                'description' => 'Property renter with limited access',
            ]);
        }
        
        // Create user role relationship in the pivot table directly
        \Illuminate\Support\Facades\DB::table('user_roles')->insert([
            'user_id' => $user->user_id,
            'role_id' => $tenantRole->role_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Tenant user created',
            'user' => $user->toArray(),
            'role' => $tenantRole->toArray()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Diagnostic route
Route::get('/diagnostic', function () {
    $output = [];
    
    // 1. Check database connection
    try {
        $dbStatus = DB::connection()->getPdo() ? 'Connected' : 'Not Connected';
        $output['database'] = ['status' => $dbStatus];
    } catch (\Exception $e) {
        $output['database'] = ['status' => 'Error', 'message' => $e->getMessage()];
    }
    
    // 2. Check if required tables exist
    $tables = ['users', 'roles', 'user_roles'];
    foreach ($tables as $table) {
        $exists = Schema::hasTable($table);
        $output['tables'][$table] = ['exists' => $exists];
        
        if ($exists) {
            $count = DB::table($table)->count();
            $output['tables'][$table]['count'] = $count;
            
            // Get sample records
            if ($count > 0) {
                $output['tables'][$table]['sample'] = DB::table($table)->first();
            }
        }
    }
    
    // 3. Check users and their roles
    try {
        $output['users'] = [];
        
        // Check admin user
        $adminUser = \App\Models\User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $output['users']['admin'] = [
                'exists' => true,
                'user_id' => $adminUser->user_id,
                'roles' => $adminUser->roles()->pluck('role_name')->toArray()
            ];
        } else {
            $output['users']['admin'] = ['exists' => false];
        }
        
        // Check landlord user
        $landlordUser = \App\Models\User::where('email', 'landlord@example.com')->first();
        if ($landlordUser) {
            $output['users']['landlord'] = [
                'exists' => true,
                'user_id' => $landlordUser->user_id,
                'roles' => $landlordUser->roles()->pluck('role_name')->toArray()
            ];
        } else {
            $output['users']['landlord'] = ['exists' => false];
        }
        
        // Check tenant user
        $tenantUser = \App\Models\User::where('email', 'tenant@example.com')->first();
        if ($tenantUser) {
            $output['users']['tenant'] = [
                'exists' => true,
                'user_id' => $tenantUser->user_id,
                'roles' => $tenantUser->roles()->pluck('role_name')->toArray()
            ];
        } else {
            $output['users']['tenant'] = ['exists' => false];
        }
        
        // Check available roles
        $output['roles'] = \App\Models\Role::all(['role_id', 'role_name', 'description'])->toArray();
        
    } catch (\Exception $e) {
        $output['user_check_error'] = ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
    }
    
    return response()->json($output);
});

// Fix admin permissions route
Route::get('/fix-admin-permissions', function () {
    try {
        // Get admin role
        $adminRole = \App\Models\Role::where('role_name', 'admin')->first();
        
        if (!$adminRole) {
            return response()->json(['error' => 'Admin role not found'], 404);
        }
        
        // Get or create system admin permission group
        $systemAdminGroup = \App\Models\PermissionGroup::firstOrCreate(
            ['group_name' => 'System Administration'],
            ['description' => 'System administration permissions']
        );
        
        // Create the required permissions for admin dashboard
        $requiredPermissions = [
            'view_admin_dashboard' => 'Access to admin dashboard',
            'manage_system_settings' => 'Manage system settings',
            'view_system_logs' => 'View system logs'
        ];
        
        $createdPermissions = [];
        
        foreach ($requiredPermissions as $permName => $description) {
            $permission = \App\Models\AccessPermission::firstOrCreate(
                [
                    'role_id' => $adminRole->role_id,
                    'permission_name' => $permName
                ],
                [
                    'description' => $description,
                    'group_id' => $systemAdminGroup->group_id
                ]
            );
            
            $createdPermissions[] = $permName;
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Admin permissions created/verified',
            'permissions' => $createdPermissions
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Fix admin role route
Route::get('/fix-admin-role', function () {
    try {
        // Create or get admin user
        $user = \App\Models\User::where('email', 'admin@example.com')->first();
        
        if (!$user) {
            return response()->json(['error' => 'Admin user not found - create one first'], 404);
        }
        
        // Get admin role
        $adminRole = \App\Models\Role::where('role_name', 'admin')->first();
        
        if (!$adminRole) {
            // Create admin role if it doesn't exist
            $adminRole = \App\Models\Role::create([
                'role_name' => 'admin',
                'description' => 'Administrator with full system access',
            ]);
        }
        
        // Check if user already has admin role
        $hasRole = \Illuminate\Support\Facades\DB::table('user_roles')
            ->where('user_id', $user->user_id)
            ->where('role_id', $adminRole->role_id)
            ->exists();
            
        if (!$hasRole) {
            // Create user role relationship in the pivot table directly
            \Illuminate\Support\Facades\DB::table('user_roles')->insert([
                'user_id' => $user->user_id,
                'role_id' => $adminRole->role_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Admin role connection verified',
            'user' => $user->username ?? $user->email,
            'role' => $adminRole->role_name
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
