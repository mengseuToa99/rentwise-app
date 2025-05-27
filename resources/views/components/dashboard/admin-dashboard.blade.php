<!-- Admin Dashboard View -->
<div class="mt-3 grid grid-cols-1 gap-3">
    <!-- Quick stats cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Property count -->
        <a href="{{ route('admin.dashboard') }}" class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Properties</div>
                        <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ $propertyCount }}</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
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
        </a>
        
        <!-- Active rentals -->
        <a href="{{ route('admin.dashboard') }}" class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Active Rentals</div>
                        <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ $rentalCount }}</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
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
        </a>
        
        <!-- Total users -->
        <a href="{{ route('admin.users') }}" class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Users</div>
                        <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ $userCount }}</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
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
        </a>
        
        <!-- Occupancy rate -->
        <a href="{{ route('admin.dashboard') }}" class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Occupancy Rate</div>
                        <div class="mt-1 text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['occupancy_rate'] ?? 0 }}%</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
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
        </a>
    </div>
    
    <!-- Financial Summary and Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Financial Summary -->
        <div class="lg:col-span-1">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Financial Summary</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                    <div class="flex items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
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
                </a>
                
                <a href="{{ route('admin.dashboard') }}" class="block overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                    <div class="flex items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
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
                </a>
                
                <a href="{{ route('admin.dashboard') }}" class="block overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
                    <div class="flex items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
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
                </a>
            </div>
        </div>
        
        <!-- Maintenance Summary -->
        <div class="lg:col-span-2">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Maintenance Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <a href="{{ route('admin.dashboard') }}" class="block overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
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
                </a>
                
                <a href="{{ route('admin.dashboard') }}" class="block overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 p-3 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
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
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent Maintenance Requests -->
    <div>
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Recent Maintenance Requests</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">View all</a>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="block overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <!-- Mobile view (card-based) -->
            <div class="block md:hidden">
                <div class="px-3 py-2 space-y-2">
                    @foreach($maintenanceSummary as $request)
                        <div class="rounded-md border border-gray-200 dark:border-zinc-700 p-2">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="h-6 w-6 flex-shrink-0 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center">
                                        <span class="text-xs font-medium">{{ substr($request['tenant']['first_name'] ?? '', 0, 1) }}{{ substr($request['tenant']['last_name'] ?? '', 0, 1) }}</span>
                                    </div>
                                    <div class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $request['tenant']['first_name'] ?? '' }} {{ $request['tenant']['last_name'] ?? '' }}
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                                    @if($request['status'] == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @elseif($request['status'] == 'approved') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($request['status'] == 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($request['status'] == 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                @endif">
                                    {{ ucfirst($request['status']) }}
                                </span>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex justify-between">
                                    <span>{{ $request['room']['room_name'] ?? '' }}</span>
                                    <span>{{ ucfirst($request['category']) }}</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span>{{ \Carbon\Carbon::parse($request['created_at'])->format('M d, Y') }}</span>
                                    <span class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Desktop view (table-based) -->
            <div class="hidden md:block">
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
                                        <span class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </a>
    </div>

    <!-- Add Calendar Section -->
    <div>
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Calendar Overview</h2>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="block overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="p-3 sm:p-4">
                <div class="h-64 sm:h-80" id="admin-calendar"></div>
            </div>
        </a>
    </div>
</div>

@script
<script>
    // Initialize admin dashboard calendar
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('admin-calendar');
        if (!calendarEl) {
            console.error('Admin calendar element not found');
            return;
        }

        console.log('Initializing admin calendar with events:', @json($stats['calendarEvents'] ?? []));

        // Fallback events if none are provided from server
        const fallbackEvents = [
            {
                id: 'inv-1',
                title: 'System Maintenance',
                start: new Date(new Date().setDate(new Date().getDate() + 3)).toISOString().split('T')[0],
                color: '#3B82F6'
            },
            {
                id: 'deadline-1',
                title: 'Tax Filing Deadline',
                start: new Date(new Date().setDate(new Date().getDate() + 10)).toISOString().split('T')[0],
                color: '#F43F5E'
            }
        ];

        // Make sure we have an array, checking both existence and type
        const serverEvents = Array.isArray(@json($stats['calendarEvents'] ?? [])) ? @json($stats['calendarEvents'] ?? []) : [];
        const events = serverEvents.length > 0 ? serverEvents : fallbackEvents;

        // Check if FullCalendar is available
        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar is not loaded. Adding script dynamically.');
            
            // Load FullCalendar stylesheet
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css';
            document.head.appendChild(link);
            
            // Load FullCalendar script
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js';
            script.onload = function() {
                console.log('FullCalendar loaded dynamically');
                initCalendar(events);
            };
            document.head.appendChild(script);
        } else {
            console.log('FullCalendar is available, initializing calendar');
            initCalendar(events);
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
                    eventDidMount: function(info) {
                        // Add tooltip with description
                        if (info.event.extendedProps.description) {
                            const tooltip = document.createElement('div');
                            tooltip.className = 'tooltip-content';
                            tooltip.innerHTML = info.event.extendedProps.description;
                            
                            info.el.appendChild(tooltip);
                            info.el.classList.add('has-tooltip');
                        }
                    },
                    eventClick: function(info) {
                        // Handle event click
                        alert(info.event.title + '\n' + (info.event.extendedProps.description || ''));
                    }
                });
                
                calendar.render();
                console.log('Admin calendar rendered successfully');
                
                // Fix contrast issues in dark mode
                fixDarkModeContrast();
                
                // Listen for dark mode changes
                const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                darkModeMediaQuery.addEventListener('change', fixDarkModeContrast);
            } catch (error) {
                console.error('Error initializing admin calendar:', error);
                
                // Show fallback message if calendar fails to initialize
                if (calendarEl) {
                    calendarEl.innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400">Unable to load calendar. Please try again later.</div>';
                }
            }
        }

        function fixDarkModeContrast() {
            const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (isDarkMode) {
                const fcHeaderElements = document.querySelectorAll('.fc-header-toolbar, .fc-daygrid-day-number, .fc-col-header-cell');
                fcHeaderElements.forEach(el => {
                    el.style.color = 'white';
                });
                
                // Additional fixes for dark mode
                const fcTableElements = document.querySelectorAll('.fc-scrollgrid, .fc-theme-standard td, .fc-theme-standard th');
                fcTableElements.forEach(el => {
                    el.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                });
            }
        }
    });
</script>
@endscript 