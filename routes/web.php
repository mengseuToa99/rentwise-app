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

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Profile
    Route::get('/profile', \App\Livewire\Profile::class)->name('profile');
    
    // Properties
    Route::get('/properties', PropertyList::class)->name('properties.index');
    Route::get('/properties/create', PropertyCreate::class)->name('properties.create');
    Route::get('/properties/{property}/edit', PropertyEdit::class)->name('properties.edit');
    Route::get('/properties/{property}', PropertyDetail::class)->name('properties.show');
    
    // Units
    Route::get('/units', UnitList::class)->name('units.index');
    Route::get('/units/create', UnitCreate::class)->name('units.create');
    Route::get('/units/{unit}/edit', UnitEdit::class)->name('units.edit');
    
    // Rentals
    Route::get('/rentals', RentalList::class)->name('rentals.index');
    Route::get('/rentals/create', RentalForm::class)->name('rentals.create');
    Route::get('/rentals/{rentalId}/edit', RentalForm::class)->name('rentals.edit');
    
    // Invoices
    Route::get('/invoices', InvoiceList::class)->name('invoices.index');
    Route::get('/invoices/create', InvoiceForm::class)->name('invoices.create');
    Route::get('/invoices/{invoiceId}/edit', InvoiceForm::class)->name('invoices.edit');
    
    // Users - Commented out until implemented
    /*
    Route::get('/users', UserList::class)->name('users.index');
    Route::get('/users/create', UserCreate::class)->name('users.create');
    Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');
    */
    
    // Messages - Commented out until implemented
    /*
    Route::get('/messages', MessageList::class)->name('messages.index');
    Route::get('/messages/{user}', MessageDetail::class)->name('messages.show');
    */
    
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Roles - Commented out until implemented
        /*
        Route::get('/roles', RoleList::class)->name('roles.index');
        Route::get('/roles/create', RoleCreate::class)->name('roles.create');
        Route::get('/roles/{role}/edit', RoleEdit::class)->name('roles.edit');
        
        // Permissions
        Route::get('/permissions', PermissionList::class)->name('permissions.index');
        Route::get('/permissions/create', PermissionCreate::class)->name('permissions.create');
        Route::get('/permissions/{permission}/edit', PermissionEdit::class)->name('permissions.edit');
        
        // Permission Groups
        Route::get('/permission-groups', PermissionGroupList::class)->name('permission-groups.index');
        Route::get('/permission-groups/create', PermissionGroupCreate::class)->name('permission-groups.create');
        Route::get('/permission-groups/{group}/edit', PermissionGroupEdit::class)->name('permission-groups.edit');
        */
    });
});

// Utility routes for landlords
Route::middleware(['auth', 'role:landlord'])->group(function () {
    Route::get('/landlord/properties', [PropertyList::class, 'landlordProperties'])->name('landlord.properties');
    Route::get('/landlord/invoices', [InvoiceList::class, 'landlordInvoices'])->name('landlord.invoices');
});

// Utility routes for tenants
Route::middleware(['auth', 'role:tenant'])->group(function () {
    Route::get('/tenant/invoices', [InvoiceList::class, 'tenantInvoices'])->name('tenant.invoices');
});

// Test route to check roles
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
})->middleware(['auth', 'role:admin']);

// Test route to create admin user
Route::get('/create-admin', function () {
    try {
        // Create or get admin user
        $user = \App\Models\UserDetail::firstOrCreate(
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
    $tables = ['user_details', 'roles', 'user_roles'];
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
    
    // 3. Create a test user with role
    try {
        // First, make sure we have the admin role
        $adminRole = \App\Models\Role::firstOrCreate(
            ['role_name' => 'admin'],
            ['description' => 'Administrator with full system access']
        );
        
        // Create the admin user
        $adminUser = \App\Models\UserDetail::firstOrCreate(
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
        
        // First check if the user already has the role
        $hasRole = DB::table('user_roles')
            ->where('user_id', $adminUser->user_id)
            ->where('role_id', $adminRole->role_id)
            ->exists();
            
        if (!$hasRole) {
            // Directly create the user_role entry
            DB::table('user_roles')->insert([
                'user_id' => $adminUser->user_id,
                'role_id' => $adminRole->role_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $output['user_creation'] = ['status' => 'Success', 'user' => $adminUser, 'role' => $adminRole, 'hasRole' => $hasRole];
        
        // Test if the role relationship works
        $user = \App\Models\UserDetail::with('roles')->find($adminUser->user_id);
        $output['roles_test'] = [
            'roles_null' => $user->roles === null,
            'roles_empty' => $user->roles->isEmpty(),
            'roles_count' => $user->roles->count(),
            'roles' => $user->roles->toArray()
        ];
        
        // Test the roles->contains method
        if ($user->roles !== null) {
            $output['contains_test'] = [
                'contains_admin' => $user->roles->contains('role_name', 'admin'),
                'contains_landlord' => $user->roles->contains('role_name', 'landlord')
            ];
        }
        
    } catch (\Exception $e) {
        $output['user_creation'] = ['status' => 'Error', 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
    }
    
    return response()->json($output);
});

// Debug routes
Route::get('/debug/user-roles', function() {
    $authUser = Auth::user();
    $roles = [];
    $userDetail = null;
    
    if ($authUser) {
        // Try to find the corresponding user detail
        $userDetail = \App\Models\UserDetail::where('email', $authUser->email)->first();
        
        if ($userDetail) {
            $roles = $userDetail->roles()->get();
        }
    }
    
    return [
        'auth_user' => $authUser,
        'user_detail' => $userDetail,
        'roles' => $roles,
        'all_roles' => \App\Models\Role::all(),
        'user_roles_count' => \App\Models\UserRole::count()
    ];
});
