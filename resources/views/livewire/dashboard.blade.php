<div class="py-2 sm:py-4 bg-gray-50 dark:bg-zinc-950">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-6">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Dashboard</h1>
        
        <!-- Admin Dashboard Link - Only for admin users -->
        @if(auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; }))
            <div class="mt-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 p-2">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-4 w-4 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-2">
                        <p class="text-xs text-blue-700 dark:text-blue-200">
                            You have admin privileges. Access the 
                            <a href="{{ route('admin.dashboard') }}" class="font-medium text-blue-700 dark:text-blue-300 underline hover:text-blue-600 dark:hover:text-blue-200">Admin Dashboard</a>
                            to manage users, roles, permissions, and system settings.
                        </p>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Tenant Dashboard View -->
        @if(auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'tenant'; }) && 
            !auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'landlord'; }) && 
            !auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; }))
            
            @include('components.dashboard.tenant-dashboard', ['stats' => $stats])
            
        @endif
        
        <!-- Landlord Dashboard View -->
        @if(auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'landlord'; }))
            
            @include('components.dashboard.landlord-dashboard', ['stats' => $stats])
            
        @endif
    </div>
</div> 