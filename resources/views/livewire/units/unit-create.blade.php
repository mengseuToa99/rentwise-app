<div class="py-4 bg-gray-50 dark:bg-zinc-950">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Add New Unit</h1>
            <div class="flex space-x-2">
                @if (!empty($propertyId))
                    <a href="{{ route('properties.show', $propertyId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back to Property
                    </a>
                @endif
                <a href="{{ route('units.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
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
            <div class="p-6 border-b border-gray-200 dark:border-zinc-800">
                <form wire:submit.prevent="create">
                    <!-- Property Information Section -->
                    <div class="border-b border-gray-200 dark:border-zinc-800 pb-5 mb-5">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Property Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div class="col-span-2">
                                <label for="propertyId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Property</label>
                                <select wire:model="propertyId" id="propertyId" class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4" {{ !empty($propertyId) ? 'disabled' : '' }}>
                                    <option value="">Select a property</option>
                                    @foreach ($properties as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('propertyId') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            @if($pricing_groups && count($pricing_groups) > 0)
                            <div class="col-span-2">
                                <label for="pricing_group_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pricing Group</label>
                                <select wire:model="pricing_group_id" id="pricing_group_id" class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4">
                                    <option value="">Select a pricing group</option>
                                    @foreach ($pricing_groups as $group)
                                        <option value="{{ $group->group_id }}">{{ $group->group_name }} ({{ $group->room_type }} - ${{ number_format($group->base_price, 2) }})</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecting a pricing group will automatically fill the room type and rent amount.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Unit Details Section -->
                    <div class="border-b border-gray-200 dark:border-zinc-800 pb-5 mb-5">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Unit Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label for="roomName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit/Room Name</label>
                                <input wire:model="roomName" type="text" id="roomName" placeholder="e.g. Master Bedroom, Studio A" class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4">
                                @error('roomName') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="roomNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit/Room Number</label>
                                <input wire:model="roomNumber" type="text" id="roomNumber" placeholder="e.g. 101, A1, 202B" class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4">
                                @error('roomNumber') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="floorNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Floor Number</label>
                                <input wire:model="floorNumber" type="number" min="1" id="floorNumber" class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4">
                                @error('floorNumber') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Type</label>
                                <select wire:model="type" id="type" class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4">
                                    <option value="">Select a type</option>
                                    <option value="Single Room">Single Room</option>
                                    <option value="Studio">Studio</option>
                                    <option value="1 Bedroom">1 Bedroom</option>
                                    <option value="2 Bedroom">2 Bedroom</option>
                                    <option value="3 Bedroom">3 Bedroom</option>
                                    <option value="4+ Bedroom">4+ Bedroom</option>
                                </select>
                                @error('type') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Size</label>
                                <input wire:model="size" type="text" id="size" placeholder="e.g. 500 sq ft" class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter size in square feet or square meters</p>
                                @error('size') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Description</label>
                                <textarea wire:model="description" id="description" rows="4" placeholder="Enter details about this unit, amenities, features, etc." class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4"></textarea>
                                @error('description') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rental Terms Section -->
                    <div class="border-b border-gray-200 dark:border-zinc-800 pb-5 mb-5">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Rental Terms</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label for="rentAmount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monthly Rent</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <input wire:model="rentAmount" type="number" min="0" step="0.01" id="rentAmount" placeholder="0.00" class="pl-7 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4">
                                </div>
                                @error('rentAmount') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="dueDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Due Date</label>
                                <input wire:model="dueDate" type="date" id="dueDate" class="block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 text-base px-4">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The day of the month when rent payment is due</p>
                                @error('dueDate') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Section -->
                    <div class="mb-5">
                        <div class="flex items-center">
                            <input wire:model="available" id="available" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-zinc-700 rounded">
                            <label for="available" class="ml-2 block text-sm text-gray-900 dark:text-white">Available for rent</label>
                            @error('available') <span class="text-red-500 dark:text-red-400 text-xs ml-2">{{ $message }}</span> @enderror
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 ml-6">If checked, this unit will be listed as available for new tenants</p>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Create Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 