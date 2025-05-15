<div class="py-2 bg-gray-50 dark:bg-zinc-950">
    <div class="mx-auto max-w-7xl px-2 sm:px-3 lg:px-4">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Dashboard</h1>
        
        <!-- Admin Dashboard Link - Case insensitive check -->
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
        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Tenant's Invoices stats -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">My Invoices</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['pendingInvoices'] + $stats['paidInvoices'] }}</div>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div class="rounded-md bg-red-50 dark:bg-red-900/20 px-3 py-1.5 text-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Pending</span>
                            <div class="font-semibold text-red-600 dark:text-red-400">{{ $stats['pendingInvoices'] }}</div>
                        </div>
                        <div class="rounded-md bg-green-50 dark:bg-green-900/20 px-3 py-1.5 text-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Paid</span>
                            <div class="font-semibold text-green-600 dark:text-green-400">{{ $stats['paidInvoices'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Upcoming Payment Card (New) -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Next Payment Due</div>
                            @php
                                $nextPayment = count($stats['upcomingInvoices']) > 0 ? $stats['upcomingInvoices'][0] : null;
                            @endphp
                            
                            @if($nextPayment)
                                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">${{ number_format($nextPayment['amount'], 2) }}</div>
                            @else
                                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">$0.00</div>
                            @endif
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    @if($nextPayment)
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Due Date:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $nextPayment['due_date'] }}</span>
                            </div>
                            
                            @if($nextPayment['days_until_due'] < 0)
                                <div class="mt-1 rounded-md bg-red-100 dark:bg-red-900/30 px-2 py-1 text-center">
                                    <span class="text-sm font-medium text-red-800 dark:text-red-300">Overdue by {{ abs($nextPayment['days_until_due']) }} days</span>
                                </div>
                            @elseif($nextPayment['days_until_due'] <= 7)
                                <div class="mt-1 rounded-md bg-yellow-100 dark:bg-yellow-900/30 px-2 py-1 text-center">
                                    <span class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Due in {{ $nextPayment['days_until_due'] }} days</span>
                                </div>
                            @else
                                <div class="mt-1 rounded-md bg-green-100 dark:bg-green-900/30 px-2 py-1 text-center">
                                    <span class="text-sm font-medium text-green-800 dark:text-green-300">Due in {{ $nextPayment['days_until_due'] }} days</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-3 rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1 text-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">No pending payments</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Lease Information Card (New) -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Lease Status</div>
                            @php
                                $activeLease = count($stats['expiringLeases']) > 0 ? $stats['expiringLeases'][0] : null;
                            @endphp
                            
                            @if($activeLease)
                                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">Active</div>
                            @else
                                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">No Active Lease</div>
                            @endif
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    
                    @if($activeLease)
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Expires:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $activeLease['end_date'] }}</span>
                            </div>
                            
                            @if($activeLease['days_until_expiry'] < 0)
                                <div class="mt-1 rounded-md bg-red-100 dark:bg-red-900/30 px-2 py-1 text-center">
                                    <span class="text-sm font-medium text-red-800 dark:text-red-300">Expired {{ abs($activeLease['days_until_expiry']) }} days ago</span>
                                </div>
                            @elseif($activeLease['days_until_expiry'] <= 30)
                                <div class="mt-1 rounded-md bg-yellow-100 dark:bg-yellow-900/30 px-2 py-1 text-center">
                                    <span class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Expires in {{ $activeLease['days_until_expiry'] }} days</span>
                                </div>
                            @else
                                <div class="mt-1 rounded-md bg-green-100 dark:bg-green-900/30 px-2 py-1 text-center">
                                    <span class="text-sm font-medium text-green-800 dark:text-green-300">Expires in {{ $activeLease['days_until_expiry'] }} days</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-3 rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1 text-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">No active lease found</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Upcoming Invoices (New) -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all sm:col-span-2">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-medium leading-6 text-gray-900 dark:text-white">Upcoming Payments</h3>
                        <a href="{{ route('tenant.invoices') }}" wire:navigate class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 hover:underline">View all</a>
                    </div>
                    <div class="mt-2 flow-root">
                        <div class="-mx-2 -my-1 overflow-x-auto sm:-mx-4 lg:-mx-6">
                            <div class="inline-block min-w-full py-1 align-middle sm:px-4 lg:px-6">
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 dark:ring-opacity-20 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                        <thead class="bg-gray-50 dark:bg-zinc-800">
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-6">Property</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Amount</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Due Date</th>
                                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pr-6">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                                            @forelse($stats['upcomingInvoices'] as $invoice)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">{{ $invoice['property_name'] }} <span class="text-gray-500 dark:text-gray-400">({{ $invoice['unit_name'] }})</span></td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900 dark:text-white">${{ number_format($invoice['amount'], 2) }}</td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $invoice['due_date'] }}
                                                        @if($invoice['days_until_due'] < 0)
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-300">Overdue</span>
                                                        @elseif($invoice['days_until_due'] <= 7)
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-300">Due soon</span>
                                                        @endif
                                                    </td>
                                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                        <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Pay now<span class="sr-only">, {{ $invoice['property_name'] }}</span></a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">No upcoming payments</td>
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
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all sm:col-span-3">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-medium leading-6 text-gray-900 dark:text-white">Calendar</h3>
                        <div class="flex space-x-2">
                            <button type="button" class="inline-flex items-center rounded border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none">
                                Today
                            </button>
                            <div class="flex rounded-md shadow-sm">
                                <button type="button" class="relative inline-flex items-center rounded-l-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:z-10 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button type="button" class="relative -ml-px inline-flex items-center rounded-r-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:z-10 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2" id="tenant-calendar" style="height: 350px;"></div>
                </div>
            </div>

            <!-- Quick access for tenant -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all sm:col-span-3">
                <div class="p-4">
                    <h3 class="text-base font-medium leading-6 text-gray-900 dark:text-white mb-3">Quick Actions</h3>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
                        <a href="{{ route('tenant.invoices') }}" wire:navigate class="flex items-center justify-center px-4 py-2.5 bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-900 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            View My Invoices
                        </a>
                        
                        <a href="{{ route('chat') }}" wire:navigate class="flex items-center justify-center px-4 py-2.5 bg-green-600 dark:bg-green-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-zinc-900 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Contact Landlord
                        </a>
                        
                        <a href="#" class="flex items-center justify-center px-4 py-2.5 bg-amber-600 dark:bg-amber-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-amber-700 dark:hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-zinc-900 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Submit Maintenance Request
                        </a>
                        
                        <a href="#" class="flex items-center justify-center px-4 py-2.5 bg-purple-600 dark:bg-purple-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-purple-700 dark:hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 dark:focus:ring-offset-zinc-900 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            View My Rental Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Landlord/Admin Dashboard View -->
        @if(auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'landlord'; }) || 
            auth()->user()->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; }))
        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Properties stats -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-2">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Properties</div>
                            <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['totalProperties'] }}</div>
                        </div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1 flex items-center text-xs">
                        <a href="{{ route('landlord.properties') }}" wire:navigate class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View all properties →</a>
                    </div>
                </div>
            </div>

            <!-- Units stats -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-2">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Units</div>
                            <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['totalUnits'] }}</div>
                        </div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="mt-2 grid grid-cols-2 gap-1">
                            <div class="rounded-md bg-green-50 dark:bg-green-900/20 px-2 py-1 text-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Occupied</span>
                                <div class="font-semibold text-green-600 dark:text-green-400">{{ $stats['occupiedUnits'] }}</div>
                            </div>
                            <div class="rounded-md bg-amber-50 dark:bg-amber-900/20 px-2 py-1 text-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Vacant</span>
                                <div class="font-semibold text-amber-600 dark:text-amber-400">{{ $stats['vacantUnits'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active rentals -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-2">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Active Rentals</div>
                            <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['activeRentals'] }}</div>
                        </div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Occupancy Rate:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $stats['occupancyRate'] }}%</span>
                        </div>
                        <div class="mt-1 w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-1.5">
                            <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $stats['occupancyRate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Income summary -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-2">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Income</div>
                            <div class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">${{ number_format($stats['totalIncome'], 2) }}</div>
                        </div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="mt-1 rounded-md bg-yellow-50 dark:bg-yellow-900/20 px-2 py-1 text-center">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Pending</span>
                                <span class="font-semibold text-yellow-600 dark:text-yellow-400">${{ number_format($stats['pendingIncome'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity, Invoices and Leases -->
        <div class="mt-3 grid grid-cols-1 gap-2 lg:grid-cols-4">
            <!-- Invoice Stats -->
            <div class="lg:col-span-1">
                <x-invoice-summary-card 
                    :totalCount="$stats['pendingInvoices'] + $stats['paidInvoices']"
                    :paidCount="$stats['paidInvoices']"
                    :pendingCount="$stats['pendingInvoices']"
                />
            </div>
            
            <!-- Expiring Leases -->
            <div class="lg:col-span-1">
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all h-full">
                    <div class="p-2">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-2">Leases Expiring Soon</h3>
                        
                        <div class="space-y-2">
                            @forelse($stats['expiringLeases'] as $lease)
                                <div class="rounded-md border border-gray-200 dark:border-zinc-700 overflow-hidden">
                                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 px-2 py-1">
                                        <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $lease['tenant_name'] }}</span>
                                        @if($lease['days_until_expiry'] < 0)
                                            <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-1.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-300">Expired</span>
                                        @elseif($lease['days_until_expiry'] <= 7)
                                            <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-1.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-300">{{ $lease['days_until_expiry'] }}d</span>
                                        @elseif($lease['days_until_expiry'] <= 30)
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-1.5 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-300">{{ $lease['days_until_expiry'] }}d</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-1.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-300">{{ $lease['days_until_expiry'] }}d</span>
                                        @endif
                                    </div>
                                    <div class="bg-gray-50 dark:bg-zinc-800 px-2 py-1 text-xs">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">Property:</span>
                                            <span class="text-gray-900 dark:text-white">{{ $lease['property_name'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">End Date:</span>
                                            <span class="text-gray-900 dark:text-white">{{ $lease['end_date'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-md bg-gray-50 dark:bg-zinc-800 p-2 text-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">No leases expiring soon</span>
                                </div>
                            @endforelse
                            
                            <a href="#" class="mt-2 block text-center px-2 py-1.5 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-medium text-xs text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none transition-colors">
                                Manage Leases
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Payments Table -->
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                    <div class="p-2">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">Recent Payments</h3>
                            <a href="#" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 hover:underline">View all</a>
                        </div>
                        
                        <div class="overflow-hidden overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                <thead class="bg-gray-50 dark:bg-zinc-800">
                                    <tr>
                                        <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Tenant</th>
                                        <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Property</th>
                                        <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Amount</th>
                                        <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                                    @forelse($stats['recentPayments'] as $payment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                            <td class="px-2 py-1.5 text-xs font-medium text-gray-900 dark:text-gray-200">{{ $payment['tenant_name'] }}</td>
                                            <td class="px-2 py-1.5 text-xs text-gray-500 dark:text-gray-400">{{ $payment['property_name'] }}</td>
                                            <td class="px-2 py-1.5 text-xs font-medium text-green-600 dark:text-green-400">${{ number_format($payment['amount'], 2) }}</td>
                                            <td class="px-2 py-1.5 text-xs text-gray-500 dark:text-gray-400">{{ $payment['paid_date'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-2 py-1.5 text-xs text-gray-500 dark:text-gray-400 text-center">No recent payments</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendar View -->
        <div class="mt-3">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-2">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">Calendar</h3>
                        <div class="flex space-x-2">
                            <button type="button" class="inline-flex items-center rounded border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none">
                                Today
                            </button>
                            <div class="flex rounded-md shadow-sm">
                                <button type="button" class="relative inline-flex items-center rounded-l-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:z-10 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button type="button" class="relative -ml-px inline-flex items-center rounded-r-md border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:z-10 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="" id="landlord-calendar" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-3">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-2">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-2">Quick Actions</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                        <a href="{{ route('landlord.properties') }}" wire:navigate class="flex flex-col items-center p-2 bg-gray-50 dark:bg-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Manage Properties</span>
                        </a>
                        
                        <a href="{{ route('landlord.invoices') }}" wire:navigate class="flex flex-col items-center p-2 bg-gray-50 dark:bg-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Manage Invoices</span>
                        </a>
                        
                        <a href="{{ route('chat') }}" wire:navigate class="flex flex-col items-center p-2 bg-gray-50 dark:bg-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Message Tenants</span>
                        </a>
                        
                        <a href="#" class="flex flex-col items-center p-2 bg-gray-50 dark:bg-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Account Settings</span>
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
                const calendarEl = document.getElementById('landlord-calendar');
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