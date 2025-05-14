<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Edit Pricing Group</h1>
                <p class="text-gray-600">Property: {{ $property->property_name }}</p>
            </div>
            <a href="{{ route('pricing-groups.index', $property->property_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Pricing Groups
            </a>
        </div>
        
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form wire:submit.prevent="update" class="space-y-6">
                    <div>
                        <label for="group_name" class="block text-sm font-medium text-gray-700">Group Name</label>
                        <input wire:model="group_name" type="text" id="group_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('group_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="room_type" class="block text-sm font-medium text-gray-700">Room Type</label>
                        <input wire:model="room_type" type="text" id="room_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('room_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700">Base Price ($)</label>
                        <input wire:model="base_price" type="number" step="0.01" min="0" id="base_price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('base_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amenities</label>
                        <div class="mt-2 space-y-2">
                            @foreach($amenities as $index => $amenity)
                                <div class="flex items-center">
                                    <input wire:model="amenities.{{ $index }}" type="text" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <button wire:click.prevent="removeAmenity({{ $index }})" class="ml-2 inline-flex items-center px-2 py-1 bg-red-100 border border-transparent rounded-md font-medium text-xs text-red-700 hover:bg-red-200">
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                            <button wire:click.prevent="addAmenity" class="inline-flex items-center px-2 py-1 bg-indigo-100 border border-transparent rounded-md font-medium text-xs text-indigo-700 hover:bg-indigo-200">
                                + Add Amenity
                            </button>
                        </div>
                        @error('amenities') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select wire:model="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-100 rounded-md">
                        <h3 class="text-sm font-medium text-yellow-800">Important Note</h3>
                        <p class="text-xs text-yellow-700 mt-1">
                            Updating this pricing group will automatically update the pricing and room type for all associated units.
                        </p>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                            Update Pricing Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
