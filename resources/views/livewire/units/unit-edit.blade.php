<div class="py-4 bg-gray-50 dark:bg-zinc-950">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Unit</h1>
            <div class="flex space-x-2">
                <a href="{{ route('units.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-zinc-900 dark:focus:ring-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Units
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-md border border-green-200 dark:border-green-800">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-md border border-red-200 dark:border-red-800">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg">
            <form wire:submit.prevent="update">
                <div class="p-6 space-y-6">
                    <!-- Property Selection -->
                    <div>
                        <label for="propertyId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Property</label>
                        <div class="mt-1">
                            <select wire:model.live="propertyId" id="propertyId" class="block w-full rounded-md bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md text-gray-900 dark:text-white shadow-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                <option value="">Select a property</option>
                                @foreach ($properties as $property)
                                    <option value="{{ $property->property_id }}">
                                        {{ $property->property_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('propertyId') 
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Room Number -->
                    <div>
                        <label for="roomNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room Number</label>
                        <div class="mt-1">
                            <input type="text" wire:model="roomNumber" id="roomNumber" class="block w-full rounded-md bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md text-gray-900 dark:text-white shadow-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                        </div>
                        @error('roomNumber') 
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Type</label>
                        <div class="mt-1">
                            <select wire:model="type" id="type" class="block w-full rounded-md bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md text-gray-900 dark:text-white shadow-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                <option value="">Select type</option>
                                <option value="Single Room">Single Room</option>
                                <option value="Studio">Studio</option>
                                <option value="1 Bedroom">1 Bedroom</option>
                                <option value="2 Bedroom">2 Bedroom</option>
                                <option value="3 Bedroom">3 Bedroom</option>
                                <option value="4+ Bedroom">4+ Bedroom</option>
                            </select>
                        </div>
                        @error('type') 
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rent Amount -->
                    <div>
                        <label for="rentAmount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monthly Rent</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                            </div>
                            <input type="number" wire:model="rentAmount" id="rentAmount" step="0.01" min="0" class="pl-7 block w-full rounded-md bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md text-gray-900 dark:text-white shadow-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                        </div>
                        @error('rentAmount') 
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <div class="mt-1">
                            <select wire:model="status" id="status" class="block w-full rounded-md bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md text-gray-900 dark:text-white shadow-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                <option value="vacant">Vacant</option>
                                <option value="occupied">Occupied</option>
                            </select>
                        </div>
                        @error('status') 
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-800 text-right space-x-2">
                    <button type="button" onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-md font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-zinc-800 dark:focus:ring-zinc-500">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 