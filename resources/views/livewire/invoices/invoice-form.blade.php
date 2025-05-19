<div class="py-4 bg-gray-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $mode === 'edit' ? 'Edit Invoice' : 'Create Invoice' }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enter invoice details and utility readings</p>
            </div>
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

        <form wire:submit="save" class="space-y-6">
            <!-- Property and Unit Selection Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Property & Unit Selection</h2>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Property Selection -->
                        <div>
                            <label for="selectedProperty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Property</label>
                            <select 
                                wire:model.live="selectedProperty" 
                                id="selectedProperty"
                                class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="">Select Property</option>
                                @foreach($properties as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('selectedProperty') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Unit Selection -->
                        <div>
                            <label for="selectedUnit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit</label>
                            <select 
                                wire:model.live="selectedUnit" 
                                id="selectedUnit"
                                class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                {{ empty($selectedProperty) ? 'disabled' : '' }}
                            >
                                <option value="">Select Unit</option>
                                @foreach($units as $id => $number)
                                    <option value="{{ $id }}">Room {{ $number }}</option>
                                @endforeach
                            </select>
                            @error('selectedUnit') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Active Rental Selection -->
                    @if(!empty($selectedUnit))
                        <div>
                            <label for="selectedRental" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Active Rental</label>
                            <select 
                                wire:model.live="selectedRental" 
                                id="selectedRental"
                                class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                                <option value="">Select Active Rental</option>
                                @if(isset($rentals) && !empty($rentals))
                                    @foreach($rentals as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('selectedRental') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- Utility Readings Card -->
            @if(!empty($selectedRental))
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Utility Readings</h2>
                    </div>
                    <div class="p-4 space-y-4">
                        @if(isset($readings) && !empty($readings))
                            @foreach($readings as $utilityId => $reading)
                                <div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $utilities[$utilityId] ?? 'Utility' }}</h3>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Previous: {{ number_format($reading['previous_reading'], 2) }}</span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">New Reading</label>
                                            <input 
                                                type="number" 
                                                wire:model.live="readings.{{ $utilityId }}.new_reading"
                                                class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                step="0.01"
                                                min="{{ $reading['previous_reading'] }}"
                                            >
                                            @error("readings.{$utilityId}.new_reading") 
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rate per Unit</label>
                                            <div class="relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                                </div>
                                                <input 
                                                    type="number" 
                                                    wire:model.live="readings.{{ $utilityId }}.rate"
                                                    class="block w-full pl-7 rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                    step="0.01"
                                                >
                                            </div>
                                            @error("readings.{$utilityId}.rate") 
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Usage:</span>
                                            <span class="text-gray-900 dark:text-white ml-1">{{ number_format($reading['amount_used'], 2) }} units</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Total:</span>
                                            <span class="text-gray-900 dark:text-white ml-1">${{ number_format($reading['total_charge'], 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-500 dark:text-gray-400">No utilities configured for this property.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Invoice Details Card -->
            @if(!empty($selectedRental))
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Invoice Details</h2>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Due Date -->
                            <div>
                                <label for="dueDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>
                                <input 
                                    type="date" 
                                    wire:model="dueDate" 
                                    id="dueDate"
                                    class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                >
                                @error('dueDate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Payment Status -->
                            <div>
                                <label for="paymentStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Status</label>
                                <select 
                                    wire:model="paymentStatus" 
                                    id="paymentStatus"
                                    class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                                @error('paymentStatus') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Total Amount -->
                        <div class="border-t border-gray-200 dark:border-zinc-700 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Total Amount</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    ${{ number_format(isset($readings) ? collect($readings)->sum('total_charge') : 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-zinc-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ $mode === 'edit' ? 'Update Invoice' : 'Create Invoice' }}
                </button>
            </div>
        </form>
    </div>
</div> 