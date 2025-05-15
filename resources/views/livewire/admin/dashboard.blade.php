<div class="py-4 bg-gray-50 dark:bg-zinc-950">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Admin Dashboard</h1>
            <div class="flex items-center space-x-2">
                <select wire:model.live="timeframe" class="rounded-md border-gray-300 py-1.5 pl-3 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                </select>
                <button type="button" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Quick stats cards -->
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Property count -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Properties</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $propertyCount }}</div>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-sm">
                        <span class="text-green-500 font-medium flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="18 15 12 9 6 15"></polyline>
                            </svg>
                            12%
                        </span>
                        <span class="ml-2 text-gray-500 dark:text-gray-400">vs last {{ $timeframe }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Active rentals -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Active Rentals</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $rentalCount }}</div>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-sm">
                        <span class="text-green-500 font-medium flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="18 15 12 9 6 15"></polyline>
                            </svg>
                            5%
                        </span>
                        <span class="ml-2 text-gray-500 dark:text-gray-400">vs last {{ $timeframe }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Total users -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Users</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $userCount }}</div>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 010 7.75"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-sm">
                        <span class="text-green-500 font-medium flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="18 15 12 9 6 15"></polyline>
                            </svg>
                            8%
                        </span>
                        <span class="ml-2 text-gray-500 dark:text-gray-400">vs last {{ $timeframe }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Occupancy rate -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 hover:shadow-md transition-all">
                <div class="p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Occupancy Rate</div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['occupancy_rate'] ?? 0 }}%</div>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 01-3.46 0"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $stats['occupancy_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Financial Summary and Charts -->
        <div class="mt-5 grid grid-cols-1 gap-3 lg:grid-cols-3">
            <!-- Financial Summary -->
            <div class="lg:col-span-1">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Financial Summary</h2>
                <div class="space-y-3">
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-100 dark:border-zinc-800">
                        <div class="flex items-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Revenue</dt>
                                <dd class="text-xl font-semibold text-green-600 dark:text-green-400 mt-1">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</dd>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-100 dark:border-zinc-800">
                        <div class="flex items-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                    <path d="M7 15h0M2 9.5h20"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Pending Payments</dt>
                                <dd class="text-xl font-semibold text-yellow-600 dark:text-yellow-400 mt-1">${{ number_format($stats['pending_payments'] ?? 0, 2) }}</dd>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-100 dark:border-zinc-800">
                        <div class="flex items-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Overdue Payments</dt>
                                <dd class="text-xl font-semibold text-red-600 dark:text-red-400 mt-1">${{ number_format($stats['overdue_payments'] ?? 0, 2) }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Maintenance Summary -->
            <div class="lg:col-span-2">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Maintenance Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-100 dark:border-zinc-800">
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200">Maintenance Requests by Status</h4>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            @foreach($stats['maintenance_by_status'] ?? [] as $status => $count)
                                <div class="flex items-center">
                                    <div class="w-3 h-3 mr-2 rounded-full 
                                        @if($status == 'pending') bg-yellow-500 
                                        @elseif($status == 'approved') bg-blue-500 
                                        @elseif($status == 'completed') bg-green-500 
                                        @elseif($status == 'rejected') bg-red-500 
                                        @endif">
                                    </div>
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($status) }}</span>
                                        <span class="ml-1 text-gray-500 dark:text-gray-400">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-3 pt-3 border-t dark:border-zinc-700">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Total Requests</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ array_sum($stats['maintenance_by_status'] ?? []) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-100 dark:border-zinc-800">
                        <h4 class="font-semibold text-gray-800 dark:text-gray-200">Maintenance by Category</h4>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            @foreach($stats['maintenance_by_category'] ?? [] as $category => $count)
                                <div class="flex items-center">
                                    <div class="w-3 h-3 mr-2 rounded-full 
                                        @if($category == 'plumbing') bg-blue-500 
                                        @elseif($category == 'electricity') bg-yellow-500 
                                        @elseif($category == 'cleaning') bg-green-500 
                                        @else bg-purple-500 
                                        @endif">
                                    </div>
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($category) }}</span>
                                        <span class="ml-1 text-gray-500 dark:text-gray-400">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if(!empty($stats['avg_resolution_time']))
                            <div class="mt-3 pt-3 border-t dark:border-zinc-700">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Avg Resolution Time</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $stats['avg_resolution_time'] }} hours</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Maintenance Requests -->
        <div class="mt-5">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Recent Maintenance Requests</h2>
                <a href="#" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">View all</a>
            </div>
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Tenant</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Room</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Category</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Status</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Date</th>
                                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-zinc-900 dark:divide-zinc-700">
                            @foreach($maintenanceSummary as $request)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-6 w-6 flex-shrink-0 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center">
                                                <span class="text-xs font-medium">{{ substr($request['tenant']['first_name'] ?? '', 0, 1) }}{{ substr($request['tenant']['last_name'] ?? '', 0, 1) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $request['tenant']['first_name'] ?? '' }} {{ $request['tenant']['last_name'] ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $request['room']['room_name'] ?? '' }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ ucfirst($request['category']) }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                                            @if($request['status'] == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                            @elseif($request['status'] == 'approved') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                            @elseif($request['status'] == 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($request['status'] == 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @endif">
                                            {{ ucfirst($request['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($request['created_at'])->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 