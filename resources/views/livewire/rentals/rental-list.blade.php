<div class="py-4 bg-gray-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Rentals</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage all your property rentals</p>
            </div>
            <a href="{{ route('rentals.create') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Rental
            </a>
        </div>
        
        @if (session('success'))
            <div class="mb-4 p-3 flex items-center text-sm text-green-600 bg-green-100 dark:bg-green-900/20 dark:text-green-400 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="mb-4 p-3 flex items-center text-sm text-red-600 bg-red-100 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif
        
        <div class="bg-white dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm mb-6">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label for="search" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                wire:model.live.debounce.300ms="search" 
                                type="text" 
                                id="search"
                                placeholder="Search rentals..." 
                                class="block w-full pl-10 py-2 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                        </div>
                    </div>
                    
                    <div class="col-span-1">
                        <label for="propertyFilter" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Property</label>
                        <select 
                            wire:model.live="propertyFilter" 
                            id="propertyFilter" 
                            class="block w-full py-2 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">All Properties</option>
                            @foreach ($properties as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-span-1">
                        <label for="statusFilter" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                        <select 
                            wire:model.live="statusFilter" 
                            id="statusFilter" 
                            class="block w-full py-2 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end mt-3 border-t dark:border-zinc-700 pt-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Rows per page:</span>
                        <select wire:model.live="perPage" class="bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md text-sm py-1 px-3 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($paginationOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        @if ($rentals->isEmpty())
            <div class="flex flex-col items-center justify-center bg-white dark:bg-zinc-900 p-8 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm">
                <div class="rounded-full bg-gray-100 dark:bg-zinc-800 p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No rentals found</h3>
                <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm mb-4">Add your first rental to start managing your property leases.</p>
                <a href="{{ route('rentals.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create your first rental
                </a>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Room</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tenant</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Start Date</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">End Date</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                            @foreach ($rentals as $rental)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $rental->property_name }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $rental->room_number }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $rental->tenant_name }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($rental->start_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $rental->end_date ? \Carbon\Carbon::parse($rental->end_date)->format('M d, Y') : 'Ongoing' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $statusClass = match($rental->status) {
                                                'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                'expired' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                'terminated' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
                                            };
                                            
                                            $statusLabel = ucfirst($rental->status);
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('invoices.create', ['rental_id' => $rental->rental_id]) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs" title="Create Invoice">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('rentals.edit', $rental->rental_id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">Edit</a>
                                            <button wire:click="deleteRental({{ $rental->rental_id }})" wire:confirm="Are you sure you want to delete this rental? This will make the room available again." class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4">
                @if($perPage !== 'all')
                    {{ $rentals->links() }}
                @else
                    <div class="text-sm text-gray-600 dark:text-gray-400 text-center">
                        Showing all {{ $rentals->count() }} results
                    </div>
                @endif
            </div>
        @endif
    </div>
</div> 