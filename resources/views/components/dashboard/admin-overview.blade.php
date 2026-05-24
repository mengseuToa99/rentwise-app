<!-- Admin Dashboard Overview -->
<div class="mt-3 grid grid-cols-1 gap-3">
    <!-- Main Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Properties stats -->
        <a href="{{ route('properties.index') }}" wire:navigate class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Properties</div>
                        <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['totalProperties'] }}</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center text-xs">
                    <span class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View all properties →</span>
                </div>
            </div>
        </a>

        <!-- Units stats -->
        <a href="{{ route('units.index') }}" wire:navigate class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Units</div>
                        <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['totalUnits'] }}</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-md bg-green-50 dark:bg-green-900/20 px-2 py-1.5 text-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Occupied</span>
                            <div class="font-semibold text-green-600 dark:text-green-400">{{ $stats['occupiedUnits'] }}</div>
                        </div>
                        <div class="rounded-md bg-amber-50 dark:bg-amber-900/20 px-2 py-1.5 text-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Vacant</span>
                            <div class="font-semibold text-amber-600 dark:text-amber-400">{{ $stats['vacantUnits'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Active rentals -->
        <a href="{{ route('rentals.index') }}" wire:navigate class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Active Rentals</div>
                        <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['activeRentals'] }}</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Occupancy Rate:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $stats['occupancyRate'] }}%</span>
                    </div>
                    <div class="mt-1 w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-1.5">
                        <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $stats['occupancyRate'] }}%"></div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Income summary -->
        <a href="{{ route('invoices.index') }}" wire:navigate class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Income</div>
                        <div class="mt-1 text-2xl sm:text-3xl font-semibold text-green-600 dark:text-green-400">${{ number_format($stats['totalIncome'], 2) }}</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/20 px-2 py-1.5 text-center">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Pending</span>
                            <span class="font-semibold text-yellow-600 dark:text-yellow-400">${{ number_format($stats['pendingIncome'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Second Row: Income chart + Top properties + Unit distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Monthly Income Chart -->
        <x-charts.spending-chart
            id="admin-income-chart"
            title="Monthly Income ({{ date('Y') }})"
            :labels="$stats['monthlyIncomeStats']['labels'] ?? []"
            :amounts="$stats['monthlyIncomeStats']['data'] ?? []"
            data-persist-chart="true"
            class="lg:col-span-1"
        />

        <!-- Top Properties -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">Top Properties</h3>
                <div class="space-y-2">
                    @forelse($stats['topProperties'] as $property)
                        <div class="rounded-md border border-gray-200 dark:border-zinc-700 px-2 py-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $property['name'] }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $property['occupied_units'] }}/{{ $property['total_units'] }} units</span>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-1.5">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $property['occupancy_rate'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-md bg-gray-50 dark:bg-zinc-800 p-2 text-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">No properties yet</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Unit Type Distribution -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">Unit Types</h3>
                <div class="space-y-2">
                    @forelse($stats['unitTypeDistribution'] as $type)
                        <div class="flex items-center justify-between rounded-md border border-gray-200 dark:border-zinc-700 px-2 py-1.5">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $type['type'] }}</span>
                            <span class="inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 px-2 py-0.5 text-xs font-medium text-indigo-800 dark:text-indigo-300">{{ $type['count'] }}</span>
                        </div>
                    @empty
                        <div class="rounded-md bg-gray-50 dark:bg-zinc-800 p-2 text-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">No units yet</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Third Row: Expiring leases + Recent payments -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <!-- Expiring Leases -->
        <div class="lg:col-span-1">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all h-full">
                <div class="p-3 sm:p-4">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">Leases Expiring Soon</h3>
                    <div class="space-y-2">
                        @forelse($stats['expiringLeases'] as $lease)
                            <div class="rounded-md border border-gray-200 dark:border-zinc-700 overflow-hidden">
                                <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 px-2 py-1.5">
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
                                <div class="bg-gray-50 dark:bg-zinc-800 px-2 py-1.5 text-xs">
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments Table -->
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all h-full">
                <div class="p-3 sm:p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">Recent Payments</h3>
                        <a href="{{ route('invoices.index') }}" wire:navigate class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 hover:underline">View all</a>
                    </div>

                    <!-- Mobile view (card-based) -->
                    <div class="block md:hidden">
                        <div class="space-y-2">
                            @forelse($stats['recentPayments'] as $payment)
                                <div class="rounded-md border border-gray-200 dark:border-zinc-700 p-2">
                                    <div class="flex justify-between items-center">
                                        <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $payment['tenant_name'] }}</div>
                                        <div class="font-semibold text-sm text-green-600 dark:text-green-400">${{ number_format($payment['amount'], 2) }}</div>
                                    </div>
                                    <div class="flex justify-between items-center mt-1 text-xs">
                                        <div class="text-gray-500 dark:text-gray-400">{{ $payment['property_name'] }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ $payment['paid_date'] }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">No recent payments</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Desktop view (table-based) -->
                    <div class="hidden md:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                <thead class="bg-gray-50 dark:bg-zinc-800">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Tenant</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Property</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Amount</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                                    @forelse($stats['recentPayments'] as $payment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                            <td class="px-3 py-2 text-xs font-medium text-gray-900 dark:text-gray-200">{{ $payment['tenant_name'] }}</td>
                                            <td class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">{{ $payment['property_name'] }}</td>
                                            <td class="px-3 py-2 text-xs font-medium text-green-600 dark:text-green-400">${{ number_format($payment['amount'], 2) }}</td>
                                            <td class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">{{ $payment['paid_date'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 text-center">No recent payments</td>
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

    <!-- Fourth Row: Calendar + Admin Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Calendar -->
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-3 sm:p-4">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">Calendar View</h3>
                    <div class="h-64 sm:h-80" id="admin-overview-calendar"></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="lg:col-span-1">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all h-full">
                <div class="p-3 sm:p-4">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('admin.users') }}" wire:navigate class="flex flex-col items-center p-2 bg-gray-50 dark:bg-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="mt-2 text-xs text-gray-600 dark:text-gray-300">Users</span>
                        </a>

                        <a href="{{ route('admin.roles') }}" wire:navigate class="flex flex-col items-center p-2 bg-gray-50 dark:bg-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span class="mt-2 text-xs text-gray-600 dark:text-gray-300">Roles</span>
                        </a>

                        <a href="{{ route('properties.index') }}" wire:navigate class="flex flex-col items-center p-2 bg-gray-50 dark:bg-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <span class="mt-2 text-xs text-gray-600 dark:text-gray-300">Properties</span>
                        </a>

                        <a href="{{ route('admin.settings') }}" wire:navigate class="flex flex-col items-center p-2 bg-gray-50 dark:bg-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="mt-2 text-xs text-gray-600 dark:text-gray-300">Settings</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Initialize admin overview calendar if the element exists
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('admin-overview-calendar');
        if (!calendarEl) {
            return;
        }

        const serverEvents = Array.isArray(@json($stats['calendarEvents'] ?? [])) ? @json($stats['calendarEvents'] ?? []) : [];

        if (typeof FullCalendar === 'undefined') {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css';
            document.head.appendChild(link);

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js';
            script.onload = function() { initCalendar(serverEvents); };
            script.onerror = showFallbackMessage;
            document.head.appendChild(script);
        } else {
            initCalendar(serverEvents);
        }

        function initCalendar(events) {
            try {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listMonth'
                    },
                    height: 'auto',
                    events: events,
                    eventClick: function(info) {
                        alert(info.event.title + '\n' + (info.event.extendedProps.description || ''));
                    }
                });
                calendar.render();

                fixDarkModeContrast();
                window.addEventListener('theme:changed', fixDarkModeContrast);
            } catch (error) {
                console.error('Error initializing admin calendar:', error);
                showFallbackMessage();
            }
        }

        function showFallbackMessage() {
            if (calendarEl) {
                calendarEl.innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400">Unable to load calendar. Please try again later.</div>';
            }
        }

        function fixDarkModeContrast() {
            const isDark = document.documentElement.classList.contains('dark');
            const calendarContainer = document.querySelector('#admin-overview-calendar .fc');
            if (calendarContainer) {
                calendarContainer.classList.toggle('dark-mode', isDark);
            }
        }
    });
</script>
@endscript

@push('scripts')
<script>
    // Force charts to reinitialize when the admin dashboard is loaded
    document.addEventListener('DOMContentLoaded', function() {
        try {
            if (typeof window.debouncedReinitializeCharts === 'function') {
                window.debouncedReinitializeCharts(true);
            }
        } catch (error) {
            console.error('Error reinitializing charts:', error);
        }
    });
</script>
@endpush
