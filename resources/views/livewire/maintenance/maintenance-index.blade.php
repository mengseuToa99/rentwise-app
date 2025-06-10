<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Maintenance Requests</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                @if($isLandlord)
                    View and manage maintenance requests for your properties.
                @else
                    Submit and track maintenance requests for your units.
                @endif
            </p>
        </div>
        @if(!$isLandlord)
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('maintenance.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                New Request
            </a>
        </div>
        @endif
    </div>

    @if (session('success'))
        <div class="mt-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <label for="search" class="sr-only">Search</label>
            <div class="relative">
                <input type="text" wire:model.debounce.300ms="search" class="block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 pr-10 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white sm:text-sm" placeholder="Search requests...">
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
        <div>
            <select wire:model="statusFilter" class="block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white sm:text-sm">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div>
            <select wire:model="priorityFilter" class="block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 focus:border-indigo-500 focus:ring-indigo-500 dark:text-white sm:text-sm">
                <option value="">All Priorities</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
        </div>
    </div>

    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300 dark:divide-zinc-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">Title</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Property</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Unit</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Priority</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Date</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                            @forelse($maintenanceRequests as $request)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                                        {{ $request->title }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $request->property->property_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $request->room->room_number }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($request->priority)
                                                @case('urgent')
                                                    bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400
                                                    @break
                                                @case('high')
                                                    bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-400
                                                    @break
                                                @case('medium')
                                                    bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400
                                                    @break
                                                @default
                                                    bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400
                                            @endswitch
                                        ">
                                            {{ ucfirst($request->priority) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($request->status)
                                                @case('pending')
                                                    bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400
                                                    @break
                                                @case('in_progress')
                                                    bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400
                                                    @break
                                                @case('completed')
                                                    bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400
                                                    @break
                                                @case('rejected')
                                                    bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400
                                                    @break
                                            @endswitch
                                        ">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $request->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        @if($isLandlord)
                                            <div class="flex justify-end space-x-2">
                                                @if($request->status === 'pending')
                                                <button wire:click="quickAction({{ $request->request_id }}, 'in_progress')" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                    Accept
                                                </button>
                                                <button wire:click="quickAction({{ $request->request_id }}, 'rejected')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    Reject
                                                </button>
                                                @endif
                                                @if($request->status === 'in_progress')
                                                <button wire:click="quickAction({{ $request->request_id }}, 'completed')" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                    Complete
                                                </button>
                                                @endif
                                                <a href="{{ route('maintenance.manage', $request->request_id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    Manage
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ route('maintenance.show', $request->request_id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                View<span class="sr-only">, {{ $request->title }}</span>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                        No maintenance requests found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 