<div class="py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Bulk Invoice Generator</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                Enter utility readings for your properties
            </p>
        </div>
        
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        
        @if(count($readings) > 0)
            <!-- Generate All Button -->
            <div class="flex justify-end mb-8">
                <button 
                    wire:click="generateInvoices" 
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Generate All Invoices
                </button>
            </div>
            
            <!-- Utility Readings Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($readings as $roomId => $utilityReadings)
                    <div 
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="bg-white dark:bg-zinc-900 rounded-2xl shadow-lg overflow-hidden transform transition-all duration-200 hover:shadow-xl"
                    >
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-xl font-bold text-white">
                                    {{ $utilityReadings[array_key_first($utilityReadings)]['property_name'] }}
                                </h3>
                            </div>
                            <div class="flex justify-between mt-3 text-sm">
                                <div class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-indigo-100">Room {{ $utilityReadings[array_key_first($utilityReadings)]['room_number'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-indigo-100">{{ $utilityReadings[array_key_first($utilityReadings)]['tenant_name'] }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Utility Readings -->
                        <div class="p-6 space-y-4">
                            @foreach($utilityReadings as $utilityId => $reading)
                                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $utilities[$utilityId] ?? 'Utility' }}
                                        </div>
                                        <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                            {{ number_format($reading['amount_used'], 2) }} units
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Previous</label>
                                            <div class="w-full p-2 bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 rounded-lg text-gray-900 dark:text-white">
                                                {{ number_format($reading['previous_reading'], 2) }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Current</label>
                                            <input
                                                type="number"
                                                wire:model.live="readings.{{ $roomId }}.{{ $utilityId }}.new_reading"
                                                class="w-full p-2 bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                step="0.01"
                                                min="{{ $reading['previous_reading'] }}"
                                            >
                                            @error("readings.{$roomId}.{$utilityId}.new_reading") 
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Payment Details -->
                        <div class="bg-gray-50 dark:bg-zinc-800 p-6 border-t border-gray-200 dark:border-zinc-700">
                            <div class="flex justify-between items-center mb-3">
                                <div class="text-sm text-gray-600 dark:text-gray-400">Rate per Unit</div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($reading['rate'], 2) }}
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400">Due Date</div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ now()->addDays(15)->format('M d, Y') }}
                                </div>
                            </div>

                            <div class="flex justify-between items-center mb-4">
                                <div class="text-base font-medium text-gray-900 dark:text-white">Total Amount</div>
                                <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                    ${{ number_format($reading['total_charge'], 2) }}
                                </div>
                            </div>

                            <!-- Generate Single Invoice Button -->
                            <button
                                wire:click="generateSingleInvoice('{{ $roomId }}')"
                                x-on:click="setTimeout(() => show = false, 100)"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Generate Invoice
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-8 text-center border border-gray-200 dark:border-zinc-700 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">No active rentals found</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">
                    There are no active rentals that need utility readings at this time.
                </p>
            </div>
        @endif
    </div>
</div>
