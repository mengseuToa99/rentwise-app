<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Admin Dashboard</h1>

        <!-- Quick stats cards -->
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Property count -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Properties</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $propertyCount }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <!-- Active rentals -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Rentals</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $rentalCount }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <!-- Total users -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-teal-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $userCount }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <!-- Occupancy rate -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-orange-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Occupancy Rate</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['occupancy_rate'] ?? 0 }}%</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <h2 class="mt-10 text-xl font-semibold text-gray-900 dark:text-white">Financial Summary</h2>
        <div class="mb-4 mt-2">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">Time Period</span>
                <select wire:model.live="timeframe" class="block w-full mt-1 text-sm border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-white dark:focus:shadow-outline-gray form-select">
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                </select>
            </label>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                <dd class="text-2xl font-semibold text-green-500 mt-2">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Pending Payments</dt>
                <dd class="text-2xl font-semibold text-yellow-500 mt-2">${{ number_format($stats['pending_payments'] ?? 0, 2) }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Overdue Payments</dt>
                <dd class="text-2xl font-semibold text-red-500 mt-2">${{ number_format($stats['overdue_payments'] ?? 0, 2) }}</dd>
            </div>
        </div>

        <!-- Maintenance Summary -->
        <h2 class="mt-10 text-xl font-semibold text-gray-900 dark:text-white">Maintenance Summary</h2>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 mt-2">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-5">
                <h4 class="mb-4 font-semibold text-gray-600 dark:text-gray-300">Maintenance Requests by Status</h4>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($stats['maintenance_by_status'] ?? [] as $status => $count)
                        <div class="flex items-center">
                            <div class="w-3 h-3 mr-2 rounded-full 
                                @if($status == 'pending') bg-yellow-500 
                                @elseif($status == 'approved') bg-blue-500 
                                @elseif($status == 'completed') bg-green-500 
                                @elseif($status == 'rejected') bg-red-500 
                                @endif">
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($status) }}: {{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-5">
                <h4 class="mb-4 font-semibold text-gray-600 dark:text-gray-300">Maintenance Requests by Category</h4>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($stats['maintenance_by_category'] ?? [] as $category => $count)
                        <div class="flex items-center">
                            <div class="w-3 h-3 mr-2 rounded-full 
                                @if($category == 'plumbing') bg-blue-500 
                                @elseif($category == 'electricity') bg-yellow-500 
                                @elseif($category == 'cleaning') bg-green-500 
                                @else bg-purple-500 
                                @endif">
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($category) }}: {{ $count }}</span>
                        </div>
                    @endforeach
                </div>
                @if(!empty($stats['avg_resolution_time']))
                    <div class="mt-4 pt-4 border-t dark:border-zinc-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Average Resolution Time: <span class="font-semibold">{{ $stats['avg_resolution_time'] }} hours</span>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Maintenance Requests -->
        <h2 class="mt-10 text-xl font-semibold text-gray-900 dark:text-white">Recent Maintenance Requests</h2>
        <div class="w-full overflow-hidden rounded-lg shadow mb-8 mt-2">
            <div class="w-full overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase dark:text-gray-400">
                            <th class="px-4 py-3">Tenant</th>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-zinc-700 dark:bg-zinc-900">
                        @foreach($maintenanceSummary as $request)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3">
                                    <div class="flex items-center text-sm">
                                        <div>
                                            <p class="font-semibold">
                                                {{ $request['tenant']['first_name'] ?? '' }} {{ $request['tenant']['last_name'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $request['room']['room_name'] ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ ucfirst($request['category']) }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-1 font-semibold leading-tight rounded-full 
                                        @if($request['status'] == 'pending') text-yellow-700 bg-yellow-100 
                                        @elseif($request['status'] == 'approved') text-blue-700 bg-blue-100 
                                        @elseif($request['status'] == 'completed') text-green-700 bg-green-100 
                                        @elseif($request['status'] == 'rejected') text-red-700 bg-red-100 
                                        @endif">
                                        {{ ucfirst($request['status']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($request['created_at'])->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 