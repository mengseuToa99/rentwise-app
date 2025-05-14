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
            
            <!-- Upcoming Payment Card (New) -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Next Payment Due</dt>
                                <dd>
                                    @php
                                        $nextPayment = count($stats['upcomingInvoices']) > 0 ? $stats['upcomingInvoices'][0] : null;
                                    @endphp
                                    
                                    @if($nextPayment)
                                        <div class="text-lg font-medium text-gray-900">${{ number_format($nextPayment['amount'], 2) }}</div>
                                        <p class="text-sm text-gray-500">Due: {{ $nextPayment['due_date'] }}</p>
                                        
                                        @if($nextPayment['days_until_due'] < 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">Overdue</span>
                                        @elseif($nextPayment['days_until_due'] <= 7)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">Due soon</span>
                                        @endif
                                    @else
                                        <div class="text-lg font-medium text-gray-900">No pending payments</div>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lease Information Card (New) -->
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Lease Status</dt>
                                <dd>
                                    @php
                                        $activeLease = count($stats['expiringLeases']) > 0 ? $stats['expiringLeases'][0] : null;
                                    @endphp
                                    
                                    @if($activeLease)
                                        <div class="text-lg font-medium text-gray-900">Active</div>
                                        <p class="text-sm text-gray-500">Expires: {{ $activeLease['end_date'] }}</p>
                                        
                                        @if($activeLease['days_until_expiry'] < 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">Expired</span>
                                        @elseif($activeLease['days_until_expiry'] <= 30)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">Expiring soon</span>
                                        @endif
                                    @else
                                        <div class="text-lg font-medium text-gray-900">No active lease</div>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Invoices (New) -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-2">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Upcoming Payments</h3>
                    <div class="mt-5 flow-root">
                        <div class="-my-2 overflow-x-auto">
                            <div class="inline-block min-w-full py-2 align-middle">
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Property</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @forelse($stats['upcomingInvoices'] as $invoice)
                                                <tr>
                                                    <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $invoice['property_name'] }} ({{ $invoice['unit_name'] }})</td>
                                                    <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">${{ number_format($invoice['amount'], 2) }}</td>
                                                    <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                                        {{ $invoice['due_date'] }}
                                                        @if($invoice['days_until_due'] < 0)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Overdue</span>
                                                        @elseif($invoice['days_until_due'] <= 7)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Due soon</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 text-center">No upcoming payments</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Calendar View (New) -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-3">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Calendar</h3>
                    <div class="mt-5" id="tenant-calendar" style="height: 350px;"></div>
                </div>
            </div>

            <!-- Quick access for tenant -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-3">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Quick Actions</h3>
                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <a href="{{ route('tenant.invoices') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            View My Invoices
                        </a>
                        
                        <a href="{{ route('chat') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Contact Landlord
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
            
            <!-- Occupancy Analysis -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-2">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Occupancy Analysis</h3>
                    <div class="mt-5">
                        <!-- Occupancy rate -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <div class="text-sm font-medium text-gray-700">Overall Occupancy Rate</div>
                                <div class="text-sm font-medium text-gray-900">{{ $stats['occupancyRate'] }}%</div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $stats['occupancyRate'] }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Property breakdown -->
                        @if(count($stats['topProperties']) > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Occupancy by Property</h4>
                                <div class="space-y-3">
                                    @foreach($stats['topProperties'] as $property)
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <div class="text-sm text-gray-600">{{ $property['name'] }}</div>
                                                <div class="text-sm text-gray-900">{{ $property['occupancy_rate'] }}%</div>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $property['occupancy_rate'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Calendar View (New) -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-4">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Calendar View</h3>
                    <div class="mt-5" id="calendar" style="height: 400px;"></div>
                </div>
            </div>
            
            <!-- Upcoming Invoices (New) -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-2">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Upcoming Invoices</h3>
                    <div class="mt-5 flow-root">
                        <div class="-my-2 overflow-x-auto">
                            <div class="inline-block min-w-full py-2 align-middle">
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Tenant</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @forelse($stats['upcomingInvoices'] as $invoice)
                                                <tr>
                                                    <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $invoice['tenant_name'] }}</td>
                                                    <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">${{ number_format($invoice['amount'], 2) }}</td>
                                                    <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                                        {{ $invoice['due_date'] }}
                                                        @if($invoice['days_until_due'] < 0)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Overdue</span>
                                                        @elseif($invoice['days_until_due'] <= 7)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Due soon</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 text-center">No upcoming invoices</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Expiring Leases (New) -->
            <div class="overflow-hidden rounded-lg bg-white shadow sm:col-span-2">
                <div class="p-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Expiring Leases</h3>
                    <div class="mt-5 flow-root">
                        <div class="-my-2 overflow-x-auto">
                            <div class="inline-block min-w-full py-2 align-middle">
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Tenant</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Property</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Expiry Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @forelse($stats['expiringLeases'] as $lease)
                                                <tr>
                                                    <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $lease['tenant_name'] }}</td>
                                                    <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">{{ $lease['property_name'] }} ({{ $lease['unit_name'] }})</td>
                                                    <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                                        {{ $lease['end_date'] }}
                                                        @if($lease['days_until_expiry'] < 0)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                                                        @elseif($lease['days_until_expiry'] <= 30)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Expiring soon</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 text-center">No expiring leases</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
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

        <!-- FullCalendar CSS -->
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet" />
        <!-- FullCalendar JS -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
        
        <!-- Initialize Calendar with FullCalendar -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tenant calendar if the element exists
                const tenantCalendarEl = document.getElementById('tenant-calendar');
                if (tenantCalendarEl) {
                    const tenantCalendar = new FullCalendar.Calendar(tenantCalendarEl, {
                        initialView: 'dayGridMonth',
                        height: 350,
                        events: @json($stats['calendarEvents']),
                        eventClick: function(info) {
                            alert(info.event.title + '\n' + info.event.extendedProps.description);
                        }
                    });
                    tenantCalendar.render();
                }
        
                // Initialize landlord/admin calendar if the element exists
                const calendarEl = document.getElementById('calendar');
                if (calendarEl) {
                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        height: 400,
                        events: @json($stats['calendarEvents']),
                        eventClick: function(info) {
                            alert(info.event.title + '\n' + info.event.extendedProps.description);
                        }
                    });
                    calendar.render();
                }
            });
        </script>
    </div>
</div> 