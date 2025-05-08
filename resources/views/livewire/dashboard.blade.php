<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        
        <!-- Admin Dashboard Link - Case insensitive check -->
        @if(auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; }))
            <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            You have admin privileges. Access the 
                            <a href="{{ route('admin.dashboard') }}" class="font-medium underline">Admin Dashboard</a>
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
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Tenant's Invoices stats -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">My Invoices</dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">{{ $stats['pendingInvoices'] + $stats['paidInvoices'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-red-600">{{ $stats['pendingInvoices'] }} pending</span>
                        <span class="mx-2">|</span>
                        <span class="font-medium text-green-600">{{ $stats['paidInvoices'] }} paid</span>
                    </div>
                </div>
            </div>

            <!-- Quick access for tenant -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-2">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Quick Access</h3>
                    <div class="mt-5 grid grid-cols-1 gap-3">
                        <a href="{{ route('tenant.invoices') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            View My Invoices
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Landlord/Admin Dashboard View -->
        @if(auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'landlord'; }) || 
            auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; }))
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Properties stats -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Properties</dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">{{ $stats['totalProperties'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Units stats -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Units</dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">{{ $stats['totalUnits'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-green-600">{{ $stats['occupiedUnits'] }} occupied</span>
                        <span class="mx-2">|</span>
                        <span class="font-medium text-blue-600">{{ $stats['vacantUnits'] }} vacant</span>
                    </div>
                </div>
            </div>

            <!-- Rentals stats -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rental Agreements</dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">{{ $stats['totalRentals'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-green-600">{{ $stats['activeRentals'] }} active</span>
                    </div>
                </div>
            </div>

            <!-- Invoices stats -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Invoices</dt>
                                <dd>
                                    <div class="text-lg font-medium text-gray-900">{{ $stats['pendingInvoices'] + $stats['paidInvoices'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-red-600">{{ $stats['pendingInvoices'] }} pending</span>
                        <span class="mx-2">|</span>
                        <span class="font-medium text-green-600">{{ $stats['paidInvoices'] }} paid</span>
                    </div>
                </div>
            </div>
            
            <!-- Financial summary -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-2">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Financial Summary</h3>
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="overflow-hidden rounded-lg bg-white border border-gray-200">
                            <div class="px-4 py-5 sm:p-6">
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Income</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">${{ number_format($stats['totalIncome'], 2) }}</dd>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-lg bg-white border border-gray-200">
                            <div class="px-4 py-5 sm:p-6">
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Income</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">${{ number_format($stats['pendingIncome'], 2) }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Admin Only: Detailed Property & Unit Statistics -->
            @if(auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; }))
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-4">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Detailed Property & Unit Analysis</h3>
                    
                    <!-- Top properties by unit count -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-800 mb-3">Top Properties by Unit Count</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Units</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupied Units</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupancy Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($stats['topProperties'] as $property)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $property['name'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $property['total_units'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $property['occupied_units'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $property['occupancy_rate'] }}%"></div>
                                                    </div>
                                                    <span>{{ $property['occupancy_rate'] }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Property and Unit Statistics -->
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <!-- Top landlords by property count -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 mb-3">Top Landlords by Property Count</h4>
                            <div class="overflow-hidden bg-white rounded-lg border border-gray-200">
                                <ul class="divide-y divide-gray-200">
                                    @forelse($stats['propertiesByLandlord'] as $landlord)
                                        <li class="px-4 py-3 flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $landlord['landlord_name'] }}</span>
                                            <span class="text-sm text-gray-600 bg-blue-100 px-2 py-1 rounded-full">{{ $landlord['count'] }} properties</span>
                                        </li>
                                    @empty
                                        <li class="px-4 py-3 text-sm text-gray-500 text-center">No data available</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- Unit type distribution -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 mb-3">Unit Types Distribution</h4>
                            <div class="overflow-hidden bg-white rounded-lg border border-gray-200">
                                <ul class="divide-y divide-gray-200">
                                    @forelse($stats['unitTypeDistribution'] as $unitType)
                                        <li class="px-4 py-3 flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $unitType['type'] }}</span>
                                            <span class="text-sm text-gray-600 bg-green-100 px-2 py-1 rounded-full">{{ $unitType['count'] }} units</span>
                                        </li>
                                    @empty
                                        <li class="px-4 py-3 text-sm text-gray-500 text-center">No data available</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- Units by Status -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 mb-3">Units by Status</h4>
                            <div class="overflow-hidden bg-white rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                    <span class="text-sm text-gray-600">Occupied: {{ $stats['occupiedUnits'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                    <span class="text-sm text-gray-600">Vacant: {{ $stats['vacantUnits'] }}</span>
                                </div>
                                <div class="mt-3">
                                    <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                                        @if(($stats['occupiedUnits'] + $stats['vacantUnits']) > 0)
                                            <div class="flex h-full">
                                                <div 
                                                    class="bg-green-500 h-full" 
                                                    style="width: {{ ($stats['occupiedUnits'] / ($stats['occupiedUnits'] + $stats['vacantUnits'])) * 100 }}%"
                                                ></div>
                                                <div 
                                                    class="bg-blue-500 h-full" 
                                                    style="width: {{ ($stats['vacantUnits'] / ($stats['occupiedUnits'] + $stats['vacantUnits'])) * 100 }}%"
                                                ></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Property/Unit Access -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 mb-3">Quick Access</h4>
                            <div class="overflow-hidden bg-white rounded-lg border border-gray-200 p-4 space-y-2">
                                <a href="{{ route('properties.index') }}" wire:navigate class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded">
                                    View All Properties
                                </a>
                                <a href="{{ route('units.index') }}" wire:navigate class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                                    View All Units
                                </a>
                                <a href="{{ route('admin.users') }}" wire:navigate class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded">
                                    Manage Users
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Property Management Quick Access -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-2">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Property Management</h3>
                    <div class="mt-5 grid grid-cols-1 gap-3">
                        <a href="{{ route('properties.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Manage Properties
                        </a>
                        <a href="{{ route('properties.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add New Property
                        </a>
                        <a href="{{ route('units.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Manage Units
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div> 