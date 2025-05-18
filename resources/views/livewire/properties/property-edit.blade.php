<div class="min-h-screen bg-gray-50 dark:bg-black">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Property</h1>
            <div class="flex space-x-2">
                <a href="{{ route('properties.show', $propertyId) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 dark:border-zinc-700 rounded-md font-medium text-sm text-black dark:text-black shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    View Property
                </a>
                <a href="{{ route('properties.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 dark:border-zinc-700 rounded-md font-medium text-sm text-black dark:text-black shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Back to Properties
                </a>
            </div>
        </div>
        
        @if (session('success'))
            <div class="p-3 text-sm text-green-600 bg-green-100/40 dark:bg-green-900/20 dark:text-green-400 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="p-3 text-sm text-red-600 bg-red-100/40 dark:bg-red-900/20 dark:text-red-400 rounded-md mb-4">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="bg-gray-50 dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-300 dark:border-black">
            <div class="p-6">
                <form wire:submit.prevent="update" class="flex flex-col gap-5">
                    <div class="grid gap-1.5">
                        <label for="property_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Property Name</label>
                        <input wire:model="property_name" type="text" id="property_name" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                        @error('property_name') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid gap-1.5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address Details</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="street_number" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Street Number</label>
                                <input wire:model="street_number" type="text" id="street_number" placeholder="Street Number" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                @error('street_number') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label for="house_number" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">House Number</label>
                                <input wire:model="house_number" type="text" id="house_number" placeholder="House Number" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                @error('house_number') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label for="village" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Village/City</label>
                                <input wire:model="village" type="text" id="village" placeholder="Village or City" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                @error('village') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label for="commune" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Commune/Sangkat</label>
                                <input wire:model="commune" type="text" id="commune" placeholder="Commune or Sangkat" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                @error('commune') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label for="district" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">District/Khan</label>
                                <input wire:model="district" type="text" id="district" placeholder="District or Khan" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                @error('district') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label for="province" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Province/City</label>
                                <input wire:model="province" type="text" id="province" placeholder="Province or City" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                @error('province') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid gap-1.5">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea wire:model="description" id="description" rows="4" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"></textarea>
                        @error('description') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid gap-1.5">
                        <label for="totalFloors" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Floors</label>
                        <input wire:model="totalFloors" type="number" min="1" id="totalFloors" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                        @error('totalFloors') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid gap-1.5">
                        <label for="totalRooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Rooms</label>
                        <input wire:model="totalRooms" type="number" min="0" id="totalRooms" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                        @error('totalRooms') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid gap-1.5">
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                        <input wire:model="location" type="text" id="location" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                        @error('location') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid gap-1.5">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select wire:model="status" id="status" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center rounded-md bg-white border border-gray-300 dark:border-zinc-700 px-5 py-2.5 text-sm font-medium text-black dark:text-black shadow-sm hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
                            <span wire:loading.remove wire:target="update">Update Property</span>
                            <span wire:loading wire:target="update">Loading...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 