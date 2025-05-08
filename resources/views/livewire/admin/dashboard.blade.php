<div>
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Admin Dashboard
        </h2>
        
        <!-- Quick stats cards -->
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
            <!-- Property count -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                        Total Properties
                    </p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                        {{ $propertyCount }}
                    </p>
                </div>
            </div>
            
            <!-- Active rentals -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                        Active Rentals
                    </p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                        {{ $rentalCount }}
                    </p>
                </div>
            </div>
            
            <!-- Total users -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <div class="p-3 mr-4 text-teal-500 bg-teal-100 rounded-full dark:text-teal-100 dark:bg-teal-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                        Total Users
                    </p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                        {{ $userCount }}
                    </p>
                </div>
            </div>
            
            <!-- Occupancy rate -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                        Occupancy Rate
                    </p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                        {{ $stats['occupancy_rate'] ?? 0 }}%
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Financial Summary -->
        <h2 class="my-6 text-xl font-semibold text-gray-700 dark:text-gray-200">
            Financial Summary
        </h2>
        
        <!-- Time period selector -->
        <div class="mb-4">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">Time Period</span>
                <select wire:model.live="timeframe" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-select">
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                </select>
            </label>
        </div>
        
        <!-- Financial cards -->
        <div class="grid gap-6 mb-8 md:grid-cols-3">
            <!-- Total Revenue -->
            <div class="p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Total Revenue
                </p>
                <p class="text-2xl font-semibold text-green-500">
                    ${{ number_format($stats['total_revenue'] ?? 0, 2) }}
                </p>
            </div>
            
            <!-- Pending Payments -->
            <div class="p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Pending Payments
                </p>
                <p class="text-2xl font-semibold text-yellow-500">
                    ${{ number_format($stats['pending_payments'] ?? 0, 2) }}
                </p>
            </div>
            
            <!-- Overdue Payments -->
            <div class="p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Overdue Payments
                </p>
                <p class="text-2xl font-semibold text-red-500">
                    ${{ number_format($stats['overdue_payments'] ?? 0, 2) }}
                </p>
            </div>
        </div>
        
        <!-- Maintenance Summary -->
        <h2 class="my-6 text-xl font-semibold text-gray-700 dark:text-gray-200">
            Maintenance Summary
        </h2>
        
        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <!-- Maintenance by Status -->
            <div class="p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <h4 class="mb-4 font-semibold text-gray-600 dark:text-gray-300">
                    Maintenance Requests by Status
                </h4>
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
            
            <!-- Maintenance by Category -->
            <div class="p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <h4 class="mb-4 font-semibold text-gray-600 dark:text-gray-300">
                    Maintenance Requests by Category
                </h4>
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
                    <div class="mt-4 pt-4 border-t dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Average Resolution Time: <span class="font-semibold">{{ $stats['avg_resolution_time'] }} hours</span>
                        </p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Recent Maintenance Requests -->
        <h2 class="my-6 text-xl font-semibold text-gray-700 dark:text-gray-200">
            Recent Maintenance Requests
        </h2>
        
        <div class="w-full overflow-hidden rounded-lg shadow-xs mb-8">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">Tenant</th>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
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