<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Utility Readings & Invoices</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Enter utility readings for units with upcoming due dates
                </p>
            </div>
            
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z" />
                </svg>
                Back to Invoices
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-8">
            <div class="flex items-center space-x-2 mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Properties Requiring Utility Readings
                </h2>
                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-indigo-900 dark:text-indigo-300">
                    {{ count($upcomingDueRentals) }}
                </span>
            </div>
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                Enter the latest utility meter readings for these properties and generate invoices automatically.
            </p>
        </div>

        @if (count($upcomingDueRentals) === 0)
            <div class="bg-white dark:bg-zinc-900 shadow rounded-lg border border-gray-200 dark:border-zinc-700 p-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No upcoming due dates</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    There are no rental units with upcoming due dates that require utility readings.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach ($upcomingDueRentals as $rental)
                    <div class="bg-white dark:bg-zinc-900 shadow rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-4 bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $rental['property_name'] }} - Room {{ $rental['room_number'] }}
                                    </h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Tenant: {{ $rental['first_name'] }} {{ $rental['last_name'] }}
                                    </p>
                                </div>
                                <div class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    Due: {{ \Carbon\Carbon::parse($rental['next_due_date'])->format('M d, Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="space-y-5">
                                @foreach ($utilities as $utilityId => $utilityName)
                                    @if(isset($readings[$rental['rental_id']][$utilityId]))
                                        <div class="border rounded-md border-gray-200 dark:border-zinc-700 p-4 @if(isset($readings[$rental['rental_id']][$utilityId]['generated']) && $readings[$rental['rental_id']][$utilityId]['generated']) bg-green-50 dark:bg-green-900/20 @endif">
                                            <h3 class="font-medium text-gray-900 dark:text-white mb-4">{{ $utilityName }}</h3>
                                            
                                            <!-- Reading details -->
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Previous Reading</label>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ number_format($readings[$rental['rental_id']][$utilityId]['old_reading'], 2) }}
                                                        @if($readings[$rental['rental_id']][$utilityId]['last_reading_date'])
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                ({{ \Carbon\Carbon::parse($readings[$rental['rental_id']][$utilityId]['last_reading_date'])->format('M d, Y') }})
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label for="new_reading_{{ $rental['rental_id'] }}_{{ $utilityId }}" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">New Reading</label>
                                                    <input 
                                                        type="number" 
                                                        wire:model.live="readings.{{ $rental['rental_id'] }}.{{ $utilityId }}.new_reading" 
                                                        id="new_reading_{{ $rental['rental_id'] }}_{{ $utilityId }}" 
                                                        step="0.01"
                                                        @if(isset($readings[$rental['rental_id']][$utilityId]['generated']) && $readings[$rental['rental_id']][$utilityId]['generated']) disabled @endif
                                                        class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm @if(isset($readings[$rental['rental_id']][$utilityId]['generated']) && $readings[$rental['rental_id']][$utilityId]['generated']) bg-gray-100 dark:bg-zinc-700 @endif"
                                                    >
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Price Per Unit</label>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        ${{ number_format($readings[$rental['rental_id']][$utilityId]['price_per_unit'], 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Calculation and invoice generation -->
                                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                                <div>
                                                    @if(isset($readings[$rental['rental_id']][$utilityId]['new_reading']) && $readings[$rental['rental_id']][$utilityId]['new_reading'] !== null)
                                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">Usage:</span> 
                                                            {{ isset($readings[$rental['rental_id']][$utilityId]['usage']) ? number_format($readings[$rental['rental_id']][$utilityId]['usage'], 2) : number_format(max(0, $readings[$rental['rental_id']][$utilityId]['new_reading'] - $readings[$rental['rental_id']][$utilityId]['old_reading']), 2) }} units
                                                        </div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            <span class="font-medium">Total:</span> 
                                                            ${{ number_format($readings[$rental['rental_id']][$utilityId]['calculated_amount'], 2) }}
                                                        </div>
                                                    @else
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            Enter a new reading to calculate
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div>
                                                    @if(isset($readings[$rental['rental_id']][$utilityId]['generated']) && $readings[$rental['rental_id']][$utilityId]['generated'])
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Invoice Generated
                                                        </span>
                                                    @else
                                                        <button 
                                                            wire:click="generateInvoice({{ $rental['rental_id'] }}, {{ $utilityId }})" 
                                                            @if(!isset($readings[$rental['rental_id']][$utilityId]['new_reading']) || $readings[$rental['rental_id']][$utilityId]['new_reading'] === null) disabled @endif
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Generate Invoice
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-8 flex justify-end">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-zinc-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Invoices
            </a>
        </div>
    </div>
</div> 