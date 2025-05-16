<!-- Tenant Dashboard View -->
<div class="mt-3 grid grid-cols-1 gap-3">
    <!-- Key Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <!-- Tenant's Invoices stats -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">My Invoices</div>
                        <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['pendingInvoices'] + $stats['paidInvoices'] }}</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <div class="rounded-md bg-red-50 dark:bg-red-900/20 px-2 py-1.5 text-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Pending</span>
                        <div class="font-semibold text-red-600 dark:text-red-400">{{ $stats['pendingInvoices'] }}</div>
                    </div>
                    <div class="rounded-md bg-green-50 dark:bg-green-900/20 px-2 py-1.5 text-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Paid</span>
                        <div class="font-semibold text-green-600 dark:text-green-400">{{ $stats['paidInvoices'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Payment Card -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Next Payment Due</div>
                        @php
                            $nextPayment = count($stats['upcomingInvoices']) > 0 ? $stats['upcomingInvoices'][0] : null;
                        @endphp
                        
                        @if($nextPayment)
                            <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">${{ number_format($nextPayment['amount'], 2) }}</div>
                        @else
                            <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">$0.00</div>
                        @endif
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                @if($nextPayment)
                    <div class="mt-2">
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
                    <div class="mt-2 rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1 text-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">No pending payments</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Lease Information Card -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Lease Status</div>
                        @php
                            $activeLease = count($stats['expiringLeases']) > 0 ? $stats['expiringLeases'][0] : null;
                        @endphp
                        
                        @if($activeLease)
                            <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">Active</div>
                        @else
                            <div class="mt-1 text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">No Active Lease</div>
                        @endif
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                
                @if($activeLease)
                    <div class="mt-2">
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
                    <div class="mt-2 rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1 text-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">No active lease found</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Spending and Utility Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <!-- Spending Chart -->
        <x-charts.spending-chart 
            id="tenant-spending-chart"
            title="Monthly Spending History"
            :labels="$stats['spendingHistory']['apex']['labels'] ?? []"
            :amounts="$stats['spendingHistory']['apex']['amounts'] ?? []"
            data-persist-chart="true"
        />
        
        <!-- Utility Usage Chart -->
        <x-charts.utility-chart 
            id="tenant-utility-chart"
            title="Utility Usage"
            :labels="$stats['utilityUsage']['apex']['labels'] ?? []"
            :electricity="$stats['utilityUsage']['apex']['electricity'] ?? []"
            :water="$stats['utilityUsage']['apex']['water'] ?? []"
            :gas="$stats['utilityUsage']['apex']['gas'] ?? []"
            data-persist-chart="true"
        />
    </div>
    
    <!-- Upcoming Invoices -->
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
        <div class="p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-base font-medium leading-6 text-gray-900 dark:text-white">Upcoming Payments</h3>
                <a href="{{ route('tenant.invoices') }}" wire:navigate class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 hover:underline">View all</a>
            </div>
            
            <!-- Mobile view (card-based) -->
            <div class="block sm:hidden">
                <div class="space-y-3">
                    @forelse($stats['upcomingInvoices'] as $invoice)
                        <div class="rounded-md border border-gray-200 dark:border-zinc-700 p-3">
                            <div class="flex justify-between items-center mb-1">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $invoice['property_name'] }}</div>
                                <div class="font-bold text-gray-900 dark:text-white">${{ number_format($invoice['amount'], 2) }}</div>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Unit: {{ $invoice['unit_name'] }}</div>
                            <div class="flex justify-between items-center mt-2">
                                <div class="text-sm">
                                    Due: {{ $invoice['due_date'] }}
                                    @if($invoice['days_until_due'] < 0)
                                        <span class="ml-1 inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-0.5 text-xs font-medium text-red-800 dark:text-red-300">Overdue</span>
                                    @elseif($invoice['days_until_due'] <= 7)
                                        <span class="ml-1 inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-300">Due soon</span>
                                    @endif
                                </div>
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Pay now</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">No upcoming payments</div>
                    @endforelse
                </div>
            </div>
            
            <!-- Desktop view (table-based) -->
            <div class="hidden sm:block">
                <div class="-mx-2 -my-1 overflow-x-auto sm:-mx-4 lg:-mx-6">
                    <div class="inline-block min-w-full py-1 align-middle sm:px-4 lg:px-6">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 dark:ring-opacity-20 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                <thead class="bg-gray-50 dark:bg-zinc-800">
                                    <tr>
                                        <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-6">Property</th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Amount</th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Due Date</th>
                                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pr-6">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                                    @forelse($stats['upcomingInvoices'] as $invoice)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                            <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">{{ $invoice['property_name'] }} <span class="text-gray-500 dark:text-gray-400">({{ $invoice['unit_name'] }})</span></td>
                                            <td class="whitespace-nowrap px-3 py-3 text-sm font-medium text-gray-900 dark:text-white">${{ number_format($invoice['amount'], 2) }}</td>
                                            <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $invoice['due_date'] }}
                                                @if($invoice['days_until_due'] < 0)
                                                    <span class="ml-2 inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:text-red-300">Overdue</span>
                                                @elseif($invoice['days_until_due'] <= 7)
                                                    <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-300">Due soon</span>
                                                @endif
                                            </td>
                                            <td class="relative whitespace-nowrap py-3 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
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
    
    <!-- Quick Actions -->
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
        <div class="p-3 sm:p-4">
            <h3 class="text-base font-medium leading-6 text-gray-900 dark:text-white mb-3">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
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
            </div>
        </div>
    </div>
</div> 