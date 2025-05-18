<div class="py-8 bg-gray-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Record Utility Usage</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Enter meter readings and generate invoices for utilities
            </p>
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

        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-zinc-800">
            <div class="p-6">
                <form wire:submit="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column - Form Inputs -->
                        <div class="space-y-6">
                            <div>
                                <label for="room_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit/Room</label>
                                <select 
                                    wire:model.live="room_id" 
                                    id="room_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                    <option value="">Select a unit</option>
                                    @foreach ($units as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('room_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="utility_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utility</label>
                                <select 
                                    wire:model.live="utility_id" 
                                    id="utility_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                    <option value="">Select a utility</option>
                                    @foreach ($utilities as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('utility_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="usage_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reading Date</label>
                                <input 
                                    type="date" 
                                    wire:model="usage_date" 
                                    id="usage_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    max="{{ date('Y-m-d') }}"
                                >
                                @error('usage_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="old_meter_reading" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Previous Reading</label>
                                    <input 
                                        type="number" 
                                        wire:model="old_meter_reading" 
                                        id="old_meter_reading" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        step="0.01"
                                        min="0"
                                        readonly
                                    >
                                    @if($previousReading)
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Last reading: {{ date('M d, Y', strtotime($previousReading->usage_date)) }}
                                        </p>
                                    @endif
                                </div>
                                
                                <div>
                                    <label for="new_meter_reading" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Reading</label>
                                    <input 
                                        type="number" 
                                        wire:model.live="new_meter_reading" 
                                        id="new_meter_reading" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        step="0.01"
                                        min="{{ $old_meter_reading }}"
                                    >
                                    @error('new_meter_reading') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 dark:border-zinc-700 pt-4">
                                <div class="flex items-center mb-4">
                                    <input 
                                        type="checkbox" 
                                        wire:model.live="create_invoice" 
                                        id="create_invoice" 
                                        class="h-4 w-4 rounded border-gray-300 dark:border-zinc-700 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                    >
                                    <label for="create_invoice" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Generate invoice for this usage
                                    </label>
                                </div>
                                
                                @if($create_invoice)
                                    <div>
                                        <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice Due Date</label>
                                        <input 
                                            type="date" 
                                            wire:model="due_date" 
                                            id="due_date" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            min="{{ date('Y-m-d') }}"
                                        >
                                        @error('due_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right Column - Summary -->
                        <div class="bg-gray-50 dark:bg-zinc-800 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Usage Summary</h3>
                            
                            @if($selectedUnit && $selectedUtility)
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit</h4>
                                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $selectedUnit->property_name }} - Room {{ $selectedUnit->room_number }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Utility</h4>
                                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $selectedUtility->utility_name }}
                                        </p>
                                    </div>
                                    
                                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-4">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Units Used</h4>
                                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                                            {{ number_format($amount_used, 2) }} units
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rate</h4>
                                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                            @if($currentPrice)
                                                ${{ number_format($currentPrice->price, 2) }} per unit
                                            @else
                                                No rate set
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-4">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Charge</h4>
                                        <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                                            ${{ number_format($calculated_charge, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No data yet</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Select a unit and utility to see usage summary
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-8 space-x-3">
                        <a 
                            href="{{ route('dashboard') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-zinc-700 border border-transparent rounded-md font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-zinc-800"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75"
                            @if(!$selectedUnit || !$selectedUtility || !$new_meter_reading) disabled @endif
                        >
                            <span wire:loading.class="hidden" wire:target="save">Record Usage</span>
                            <span wire:loading wire:target="save">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 