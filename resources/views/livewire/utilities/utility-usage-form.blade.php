<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Record Utility Usage</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Record utility consumption and generate invoices automatically
            </p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-zinc-700">
            <div class="p-6">
                <form wire:submit="save">
                    <!-- Step 1: Select Property -->
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Step 1: Select Property</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="property_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Property</label>
                                <select 
                                    wire:model.live="property_id" 
                                    id="property_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                    <option value="">Select a property</option>
                                    @foreach ($properties as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('property_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            @if($property_id)
                                <div>
                                    <label for="room_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room/Unit</label>
                                    <select 
                                        wire:model.live="room_id" 
                                        id="room_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                        <option value="">Select a room/unit</option>
                                        @foreach ($rooms as $id => $number)
                                            <option value="{{ $id }}">Room {{ $number }}</option>
                                        @endforeach
                                    </select>
                                    @error('room_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($room_id)
                        <!-- Step 2: Select Utility -->
                        <div class="mb-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Step 2: Select Utility</h2>
                            <div>
                                <label for="utility_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utility Type</label>
                                <select 
                                    wire:model.live="utility_id" 
                                    id="utility_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                    <option value="">Select a utility</option>
                                    @foreach ($utilities as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('utility_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    @if($utility_id)
                        <!-- Step 3: Enter Meter Reading -->
                        <div class="mb-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Step 3: Enter Meter Reading</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="usage_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reading Date</label>
                                    <input 
                                        type="date" 
                                        wire:model="usage_date" 
                                        id="usage_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        max="{{ date('Y-m-d') }}"
                                    >
                                    @error('usage_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="old_meter_reading" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Previous Reading</label>
                                    <input 
                                        type="number" 
                                        wire:model="old_meter_reading" 
                                        id="old_meter_reading" 
                                        step="0.01"
                                        readonly
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white bg-gray-100 dark:bg-zinc-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Last reading from {{ $previousReading ? Carbon\Carbon::parse($previousReading->usage_date)->format('M d, Y') : 'None (first reading)' }}
                                    </p>
                                </div>
                                
                                <div>
                                    <label for="new_meter_reading" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Reading</label>
                                    <input 
                                        type="number" 
                                        wire:model.live="new_meter_reading" 
                                        id="new_meter_reading" 
                                        step="0.01"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                    @error('new_meter_reading') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="amount_used" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Usage Amount</label>
                                    <input 
                                        type="number" 
                                        wire:model="amount_used" 
                                        id="amount_used" 
                                        step="0.01"
                                        readonly
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white bg-gray-100 dark:bg-zinc-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Calculate Charge -->
                        <div class="mb-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Step 4: Calculate Charge</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="rate_per_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rate Per Unit ($)</label>
                                    <input 
                                        type="number" 
                                        wire:model.live="rate_per_unit" 
                                        id="rate_per_unit" 
                                        step="0.01"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                    @error('rate_per_unit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        You can adjust the rate if needed
                                    </p>
                                </div>
                                
                                <div>
                                    <label for="calculated_charge" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Charge ($)</label>
                                    <input 
                                        type="number" 
                                        wire:model="calculated_charge" 
                                        id="calculated_charge" 
                                        step="0.01"
                                        readonly
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white bg-gray-100 dark:bg-zinc-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                    <div class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                        <span class="font-medium">Calculation:</span> {{ $amount_used ?? 0 }} units × ${{ number_format($rate_per_unit ?? 0, 2) }} = ${{ number_format($calculated_charge ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: Generate Invoice -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-zinc-800 rounded-md border border-gray-200 dark:border-zinc-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Step 5: Generate Invoice</h2>
                            
                            <div class="flex items-center mb-4">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="create_invoice" 
                                    id="create_invoice" 
                                    class="h-4 w-4 rounded border-gray-300 dark:border-zinc-700 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                >
                                <label for="create_invoice" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Generate invoice for this utility usage
                                </label>
                            </div>
                            
                            @if($create_invoice)
                                <div>
                                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice Due Date</label>
                                    <input 
                                        type="date" 
                                        wire:model="due_date" 
                                        id="due_date" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        min="{{ date('Y-m-d') }}"
                                    >
                                    @error('due_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <a href="{{ route('utilities.usage.index') }}" class="mr-3 px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600 transition-colors">
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            {{ (!$room_id || !$utility_id || !$new_meter_reading) ? 'disabled' : '' }}
                        >
                            Save Usage {{ $create_invoice ? 'and Generate Invoice' : '' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 