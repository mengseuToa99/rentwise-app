<div class="text-[hsl(var(--foreground))]">
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-between items-center my-6">
            <h2 class="text-2xl font-semibold text-[hsl(var(--foreground))]">
                System Logs
            </h2>
            
            <div class="flex items-center space-x-3">
                @include('livewire.components.dark-mode-toggle')
                
                <button wire:click="exportLogs" 
                        class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Export Logs</span>
                </button>
            </div>
        </div>
        
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm dark:bg-green-900/50 dark:border-green-600 dark:text-green-100" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm dark:bg-red-900/50 dark:border-red-600 dark:text-red-100" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        @if (session()->has('info'))
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded shadow-sm dark:bg-blue-900/50 dark:border-blue-600 dark:text-blue-100" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p>{{ session('info') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Filters Card -->
        <div class="mb-6 px-4 py-3 bg-[hsl(var(--card))] dark:bg-[#111111] rounded-lg shadow-md text-[hsl(var(--card-foreground))]">
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-sm font-semibold text-[hsl(var(--card-foreground))]">Filter Logs</h4>
                <button wire:click="clearFilters" 
                        class="flex items-center px-3 py-1 text-xs font-medium text-[hsl(var(--muted-foreground))] transition-colors duration-150 border border-[hsl(var(--border))] rounded-md hover:bg-[hsl(var(--muted))] dark:hover:bg-[#192338] focus:outline-none focus:ring-1 focus:ring-[hsl(var(--ring))]">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Clear
                </button>
            </div>
            
            <div class="grid gap-4 md:grid-cols-4 md:gap-6 items-end">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))]">
                        Search
                    </label>
                    <div class="relative text-[hsl(var(--muted-foreground))]">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" 
                               class="block w-full pl-10 pr-3 py-2 text-sm text-[hsl(var(--foreground))] dark:text-gray-300 placeholder-[hsl(var(--muted-foreground))] dark:placeholder-gray-500 border border-[hsl(var(--input))] dark:border-gray-600 rounded-md bg-[hsl(var(--background))] dark:bg-[#0f172a] focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400" 
                               placeholder="Search logs..." 
                               type="text" />
                    </div>
                </div>
                
                <!-- Action Filter -->
                <div>
                    <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))]">
                        Action Type
                    </label>
                    <select wire:model.live="actionFilter" 
                            class="block w-full py-2 text-sm text-[hsl(var(--foreground))] dark:text-gray-300 border border-[hsl(var(--input))] dark:border-gray-600 rounded-md bg-[hsl(var(--background))] dark:bg-[#0f172a] focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400">
                        <option value="">All Actions</option>
                        @foreach($actionTypes as $action)
                            <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))]">
                        Date From
                    </label>
                    <input wire:model.live="dateFrom" 
                           class="block w-full py-2 text-sm text-[hsl(var(--foreground))] dark:text-gray-300 border border-[hsl(var(--input))] dark:border-gray-600 rounded-md bg-[hsl(var(--background))] dark:bg-[#0f172a] focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400" 
                           type="date" />
                </div>
                
                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))]">
                        Date To
                    </label>
                    <input wire:model.live="dateTo" 
                           class="block w-full py-2 text-sm text-[hsl(var(--foreground))] dark:text-gray-300 border border-[hsl(var(--input))] dark:border-gray-600 rounded-md bg-[hsl(var(--background))] dark:bg-[#0f172a] focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-400" 
                           type="date" />
                </div>
            </div>
        </div>
        
        <!-- Logs Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-md mb-8 bg-[hsl(var(--card))] dark:bg-[#111111]">
            @if(count($logs) > 0)
                <!-- Log Activity Summary -->
                <div class="flex flex-wrap gap-2 p-4 border-b border-[hsl(var(--border))] dark:border-[#1e293b]">
                    @php
                        $actionCounts = [];
                        foreach($logs as $log) {
                            if (!isset($actionCounts[$log->action])) {
                                $actionCounts[$log->action] = 0;
                            }
                            $actionCounts[$log->action]++;
                        }
                    @endphp
                    
                    @foreach($actionCounts as $action => $count)
                        <span class="px-3 py-1 text-xs rounded-full 
                            @if(str_contains($action, 'create')) text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/50
                            @elseif(str_contains($action, 'update')) text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/50
                            @elseif(str_contains($action, 'delete')) text-red-700 bg-red-100 dark:text-red-300 dark:bg-red-900/50
                            @elseif(str_contains($action, 'login')) text-purple-700 bg-purple-100 dark:text-purple-300 dark:bg-purple-900/50
                            @else text-[hsl(var(--foreground))] bg-[hsl(var(--muted))] dark:bg-[#1e293b]
                            @endif">
                            {{ ucfirst($action) }}: {{ $count }}
                        </span>
                    @endforeach
                </div>
            @endif
            
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left uppercase border-b border-[hsl(var(--border))] bg-[hsl(var(--muted))] dark:bg-[#1e1e1e] dark:border-[#333333] dark:text-white">
                            <th class="px-4 py-3">Time</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[hsl(var(--card))] dark:bg-[#111111] divide-y divide-[hsl(var(--border))] dark:divide-[#1e1e1e]">
                        @forelse($logs as $log)
                            <tr class="text-[hsl(var(--card-foreground))] hover:bg-[hsl(var(--muted))] dark:hover:bg-[#1a1a1a]">
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-mono">{{ \Carbon\Carbon::parse($log->timestamp)->format('Y-m-d') }}</span>
                                    <br>
                                    <span class="text-xs text-[hsl(var(--muted-foreground))]">{{ \Carbon\Carbon::parse($log->timestamp)->format('H:i:s') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                                            <div class="absolute inset-0 rounded-full bg-[hsl(var(--muted))] dark:bg-[#1a1a1a] flex items-center justify-center">
                                                <span class="text-xs font-medium text-[hsl(var(--muted-foreground))]">
                                                    {{ $log->user && $log->user->username ? strtoupper(substr($log->user->username, 0, 1)) : '?' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold">{{ $log->user->username ?? 'Unknown' }}</p>
                                            <p class="text-xs text-[hsl(var(--muted-foreground))]">{{ $log->user->email ?? 'Unknown' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                        @if(str_contains($log->action, 'create')) text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/50
                                        @elseif(str_contains($log->action, 'update')) text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/50
                                        @elseif(str_contains($log->action, 'delete')) text-red-700 bg-red-100 dark:text-red-300 dark:bg-red-900/50
                                        @elseif(str_contains($log->action, 'login')) text-purple-700 bg-purple-100 dark:text-purple-300 dark:bg-purple-900/50
                                        @else text-[hsl(var(--foreground))] bg-[hsl(var(--muted))] dark:bg-[#1e293b]
                                        @endif">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @empty
                            <tr class="text-[hsl(var(--muted-foreground))]">
                                <td colspan="4" class="px-4 py-8 text-sm text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-[hsl(var(--muted-foreground))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p class="mt-3 text-lg font-semibold text-[hsl(var(--foreground))]">No logs found</p>
                                        <p class="text-[hsl(var(--muted-foreground))]">Try adjusting your search or filter parameters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(count($logs) > 0)
                <div class="px-4 py-3 border-t border-[hsl(var(--border))] dark:border-[#1e293b] text-[hsl(var(--muted-foreground))]">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div> 