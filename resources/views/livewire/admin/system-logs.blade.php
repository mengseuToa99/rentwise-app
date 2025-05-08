<div>
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            System Logs
        </h2>
        
        <!-- Filters -->
        <div class="mb-6 px-4 py-3 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="grid gap-6 md:grid-cols-4 items-end">
                <!-- Search -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Search</span>
                        <input wire:model.live.debounce.300ms="search" 
                               class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" 
                               placeholder="Search logs..." 
                               type="text" />
                    </label>
                </div>
                
                <!-- Action Filter -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Action Type</span>
                        <select wire:model.live="actionFilter" 
                                class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-select">
                            <option value="">All Actions</option>
                            @foreach($actionTypes as $action)
                                <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                
                <!-- Date From -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Date From</span>
                        <input wire:model.live="dateFrom" 
                               class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" 
                               type="date" />
                    </label>
                </div>
                
                <!-- Date To -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Date To</span>
                        <input wire:model.live="dateTo" 
                               class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" 
                               type="date" />
                    </label>
                </div>
            </div>
            
            <div class="flex justify-between mt-4">
                <button wire:click="clearFilters" 
                        class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-gray-600 border border-transparent rounded-lg active:bg-gray-600 hover:bg-gray-700 focus:outline-none focus:shadow-outline-gray">
                    Clear Filters
                </button>
                
                <button wire:click="exportLogs" 
                        class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Export Logs
                </button>
            </div>
        </div>
        
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        @if (session()->has('info'))
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                <p>{{ session('info') }}</p>
            </div>
        @endif
        
        <!-- Logs Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-xs mb-8">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">Time</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($logs as $log)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($log->timestamp)->format('M d, Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center text-sm">
                                        <div>
                                            <p class="font-semibold">{{ $log->user->username ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $log->user->email ?? 'Unknown' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 font-semibold leading-tight rounded-full 
                                        @if(str_contains($log->action, 'create')) text-green-700 bg-green-100 
                                        @elseif(str_contains($log->action, 'update')) text-blue-700 bg-blue-100 
                                        @elseif(str_contains($log->action, 'delete')) text-red-700 bg-red-100 
                                        @elseif(str_contains($log->action, 'login')) text-purple-700 bg-purple-100 
                                        @else text-gray-700 bg-gray-100 
                                        @endif">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @empty
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td colspan="4" class="px-4 py-3 text-sm text-center">
                                    No logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div> 