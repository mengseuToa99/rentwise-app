<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Add New Unit</h1>
            <div class="flex space-x-2">
                @if (!empty($propertyId))
                    <a href="{{ route('properties.show', $propertyId) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Back to Property
                    </a>
                @endif
                <a href="{{ route('units.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Units
                </a>
            </div>
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
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form wire:submit.prevent="create" class="space-y-6">
                    <div>
                        <label for="propertyId" class="block text-sm font-medium text-gray-700">Property</label>
                        <select wire:model="propertyId" id="propertyId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" {{ !empty($propertyId) ? 'disabled' : '' }}>
                            <option value="">Select a property</option>
                            @foreach ($properties as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('propertyId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    @if($pricing_groups && count($pricing_groups) > 0)
                    <div>
                        <label for="pricing_group_id" class="block text-sm font-medium text-gray-700">Pricing Group</label>
                        <select wire:model="pricing_group_id" id="pricing_group_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Select a pricing group</option>
                            @foreach ($pricing_groups as $group)
                                <option value="{{ $group->group_id }}">{{ $group->group_name }} ({{ $group->room_type }} - ${{ number_format($group->base_price, 2) }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Selecting a pricing group will automatically fill the room type and rent amount.</p>
                    </div>
                    @endif
                    
                    <div>
                        <label for="roomName" class="block text-sm font-medium text-gray-700">Unit/Room Name</label>
                        <input wire:model="roomName" type="text" id="roomName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('roomName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="roomNumber" class="block text-sm font-medium text-gray-700">Unit/Room Number</label>
                        <input wire:model="roomNumber" type="text" id="roomNumber" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('roomNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="floorNumber" class="block text-sm font-medium text-gray-700">Floor Number</label>
                        <input wire:model="floorNumber" type="number" min="1" id="floorNumber" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('floorNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select wire:model="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Select a type</option>
                            <option value="Single Room">Single Room</option>
                            <option value="Studio">Studio</option>
                            <option value="1 Bedroom">1 Bedroom</option>
                            <option value="2 Bedroom">2 Bedroom</option>
                            <option value="3 Bedroom">3 Bedroom</option>
                            <option value="4+ Bedroom">4+ Bedroom</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700">Size</label>
                        <input wire:model="size" type="text" id="size" placeholder="e.g. 500 sq ft" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('size') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="rentAmount" class="block text-sm font-medium text-gray-700">Monthly Rent</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input wire:model="rentAmount" type="number" min="0" step="0.01" id="rentAmount" class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        @error('rentAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="dueDate" class="block text-sm font-medium text-gray-700">Payment Due Date</label>
                        <input wire:model="dueDate" type="date" id="dueDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('dueDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex items-center">
                        <input wire:model="available" id="available" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="available" class="ml-2 block text-sm text-gray-900">Available for rent</label>
                        @error('available') <span class="text-red-500 text-xs ml-2">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                            Create Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 