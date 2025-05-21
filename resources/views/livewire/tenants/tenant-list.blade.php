<div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Tenant Information</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View and manage information about tenants renting your properties.</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="mb-5 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4">
                <!-- Search -->
                <div>
                    <label for="search" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" name="search" id="search" class="block w-full rounded-md border-gray-300 pl-10 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white dark:placeholder-gray-400 sm:text-sm" placeholder="Search tenants...">
                    </div>
                </div>

                <!-- Property Filter -->
                <div>
                    <label for="propertyFilter" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Property</label>
                    <select wire:model.live="propertyFilter" id="propertyFilter" name="propertyFilter" class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white sm:text-sm">
                        <option value="">All Properties</option>
                        @foreach($properties as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="statusFilter" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Rental Status</label>
                    <select wire:model.live="statusFilter" id="statusFilter" name="statusFilter" class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white sm:text-sm">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Items Per Page -->
                <div>
                    <label for="perPage" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Show</label>
                    <select wire:model.live="perPage" id="perPage" name="perPage" class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white sm:text-sm">
                        @foreach($paginationOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tenants List -->
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-zinc-900">
            <!-- Mobile view (card-based) -->
            <div class="block md:hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tenants as $tenant)
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ $tenant->first_name }} {{ $tenant->last_name }}
                                    </h3>
                                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $tenant->email }}
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $tenant->phone_number }}
                                    </div>
                                </div>
                                <div>
                                    @if($tenant->rental_status === 'active')
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">Active</span>
                                    @elseif($tenant->rental_status === 'expired')
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">Expired</span>
                                    @elseif($tenant->rental_status === 'terminated')
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">Terminated</span>
                                    @elseif($tenant->rental_status === 'pending')
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Pending</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-500 dark:text-gray-400">Property:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $tenant->property_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-500 dark:text-gray-400">Unit:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $tenant->room_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-500 dark:text-gray-400">Start Date:</span>
                                    <span class="text-gray-900 dark:text-white">{{ date('M d, Y', strtotime($tenant->start_date)) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-500 dark:text-gray-400">End Date:</span>
                                    <span class="text-gray-900 dark:text-white">{{ date('M d, Y', strtotime($tenant->end_date)) }}</span>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button wire:click="viewTenantDetails({{ $tenant->user_id }})" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-xs font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-700 dark:hover:bg-blue-600">
                                    View Details
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            No tenants found.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Desktop view (table-based) -->
            <div class="hidden md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tenant</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Contact</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Property</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Unit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Lease Period</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-zinc-900">
                            @forelse($tenants as $tenant)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                    <span class="text-sm font-medium">{{ substr($tenant->first_name, 0, 1) }}{{ substr($tenant->last_name, 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->first_name }} {{ $tenant->last_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $tenant->email }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $tenant->phone_number }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $tenant->property_name }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $tenant->room_number }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ date('M d, Y', strtotime($tenant->start_date)) }} - {{ date('M d, Y', strtotime($tenant->end_date)) }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($tenant->rental_status === 'active')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">Active</span>
                                        @elseif($tenant->rental_status === 'expired')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">Expired</span>
                                        @elseif($tenant->rental_status === 'terminated')
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">Terminated</span>
                                        @elseif($tenant->rental_status === 'pending')
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Pending</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <button wire:click="viewTenantDetails({{ $tenant->user_id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View Details</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No tenants found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($tenants instanceof \Illuminate\Pagination\LengthAwarePaginator && $perPage !== 'all')
                <div class="border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-zinc-900 sm:px-6">
                    {{ $tenants->links() }}
                </div>
            @endif
        </div>
    </div>
</div> 