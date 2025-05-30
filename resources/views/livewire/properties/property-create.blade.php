<div class="min-h-screen bg-gray-50 dark:bg-black">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Add New Property</h1>
            <a href="{{ route('properties.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-zinc-900 dark:focus:ring-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Properties
            </a>
        </div>
        
        @if (session('error'))
            <div class="p-3 text-sm text-red-600 bg-red-100/40 dark:bg-red-900/20 dark:text-red-400 rounded-md mb-4">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="bg-white dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-300 dark:border-zinc-700 shadow-sm">
            <form wire:submit.prevent="create" class="flex flex-col">
                <!-- Basic Information Section -->
                <div class="border-b border-gray-300 dark:border-zinc-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="grid gap-1.5">
                            <label for="property_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Property Name*</label>
                            <input wire:model="property_name" type="text" id="property_name" placeholder="Enter property name" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                        @error('property_name') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid gap-1.5">
                            <label for="propertyType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Property Type*</label>
                            <select wire:model="propertyType" id="propertyType" class="block w-full rounded-md bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md text-gray-900 dark:text-white shadow-none focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                @foreach($propertyTypes as $value => $label)
                                    <option value="{{ $value }}" class="bg-white dark:bg-zinc-800">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('propertyType') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="grid gap-1.5 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address Details*</label>
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
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City/Location*</label>
                            <input wire:model="location" type="text" id="location" placeholder="City or area name" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                            @error('location') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="grid gap-1.5">
                            <label for="yearBuilt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year Built</label>
                            <input wire:model="yearBuilt" type="number" id="yearBuilt" min="1800" max="2100" placeholder="Year of construction" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                            @error('yearBuilt') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Property Details Section -->
                <div class="border-b border-gray-300 dark:border-zinc-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Property Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="grid gap-1.5">
                            <label for="totalFloors" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Floors*</label>
                            <input wire:model.live="totalFloors" type="number" min="1" id="totalFloors" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                            @error('totalFloors') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="grid gap-1.5">
                            <label for="totalRooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Rooms*</label>
                            <input wire:model="totalRooms" type="number" min="0" id="totalRooms" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                            @error('totalRooms') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="grid gap-1.5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Property Size</label>
                            <div class="flex gap-2">
                                <input wire:model="propertySize" type="number" min="1" placeholder="Size" class="block w-2/3 rounded-md bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md text-gray-900 dark:text-white shadow-none focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                <select wire:model="sizeMeasurement" class="block w-1/3 rounded-md bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 px-2 py-3 text-md text-gray-900 dark:text-white shadow-none focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                    @foreach($sizeUnits as $value => $label)
                                        <option value="{{ $value }}" class="bg-white dark:bg-zinc-800">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('propertySize') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description*</label>
                        <textarea wire:model="description" id="description" rows="4" placeholder="Detailed description of the property" class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"></textarea>
                        @error('description') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <!-- Auto Generate Units Section -->
                <div class="border-b border-gray-300 dark:border-zinc-700 p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Auto Generate Units</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Save time by automatically creating units for all floors</p>
                        </div>
                        
                        <div class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="mr-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.live="autoGenerateUnits" class="sr-only peer">
                                    <div class="relative w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $autoGenerateUnits ? 'Enabled' : 'Disabled' }}</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $autoGenerateUnits ? 'Units will be created automatically' : 'Toggle to enable auto-generation' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($autoGenerateUnits)
                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-5 border border-gray-200 dark:border-zinc-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="grid gap-1.5">
                                <label for="unitsPerFloor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center">
                                        <span>Units Per Floor</span>
                                        <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">(how many units on each floor)</span>
                                    </div>
                                </label>
                                <div class="flex items-center">
                                    <input wire:model.live="unitsPerFloor" type="number" min="1" id="unitsPerFloor" placeholder="e.g. 4" class="block w-full rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <div class="ml-2 text-gray-500 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('unitsPerFloor') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="grid gap-1.5">
                                <label for="unitNamingConvention" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center">
                                        <span>Unit Naming Convention</span>
                                        <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">(how units will be numbered)</span>
                                    </div>
                                </label>
                                <select wire:model.live="unitNamingConvention" id="unitNamingConvention" class="block w-full rounded-md bg-gray-50 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md text-gray-900 dark:text-white shadow-none focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                                    <option value="numeric" class="bg-white dark:bg-zinc-800">Numeric (101, 102, 201, 202)</option>
                                    <option value="letter" class="bg-white dark:bg-zinc-800">Letter (1A, 1B, 2A, 2B)</option>
                                    <option value="ordinal" class="bg-white dark:bg-zinc-800">Ordinal (1st Floor Room 1, 1st Floor Room 2)</option>
                                </select>
                                @error('unitNamingConvention') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="grid gap-1.5">
                                <label for="defaultRentAmount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center">
                                        <span>Default Rent Amount</span>
                                        <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">(applied to all units)</span>
                                    </div>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400">$</span>
                                    </div>
                                    <input wire:model.live="defaultRentAmount" type="number" min="0" step="0.01" id="defaultRentAmount" placeholder="e.g. 1000.00" class="block w-full pl-8 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                @error('defaultRentAmount') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="grid gap-1.5">
                                <label for="defaultUnitType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center">
                                        <span>Default Unit Type</span>
                                        <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">(e.g. Studio, 1BHK)</span>
                                    </div>
                                </label>
                                <input wire:model.live="defaultUnitType" type="text" id="defaultUnitType" placeholder="e.g. Studio, 1BHK" class="block w-full rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('defaultUnitType') <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        @if($totalFloors > 0 && $unitsPerFloor > 0)
                        <div class="bg-white dark:bg-zinc-900 rounded-lg p-5 border border-gray-200 dark:border-zinc-700">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-md font-medium text-gray-800 dark:text-white">Preview of Units</h3>
                                <span class="text-sm text-blue-600 dark:text-blue-400">Total: {{ $totalFloors * $unitsPerFloor }} units</span>
                            </div>
                            
                            <div class="space-y-4">
                                @php
                                $maxFloorsToShow = min($totalFloors, 3); // Show max 3 floors in preview
                                $maxUnitsPerFloorToShow = min($unitsPerFloor, 8); // Show max 8 units per floor
                                @endphp
                                
                                @for($floor = 1; $floor <= $maxFloorsToShow; $floor++)
                                    <div class="space-y-2">
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Floor {{ $floor }}</h4>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-2">
                                            @for($unit = 1; $unit <= $maxUnitsPerFloorToShow; $unit++)
                                                @php
                                                $unitName = '';
                                                if($unitNamingConvention === 'numeric') {
                                                    $unitName = $floor . str_pad($unit, 2, '0', STR_PAD_LEFT);
                                                } elseif($unitNamingConvention === 'letter') {
                                                    $unitName = $floor . chr(64 + $unit);
                                                } else {
                                                    $unitName = $floor . '-' . $unit;
                                                }
                                                @endphp
                                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-2 rounded-md text-xs text-center text-blue-700 dark:text-blue-300 font-medium">
                                                    {{ $unitName }}
                                                </div>
                                            @endfor
                                            
                                            @if($unitsPerFloor > $maxUnitsPerFloorToShow)
                                                <div class="flex items-center justify-center bg-gray-50 dark:bg-zinc-800 p-2 rounded-md text-xs text-gray-500 dark:text-gray-400">
                                                    +{{ $unitsPerFloor - $maxUnitsPerFloorToShow }} more
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endfor
                                
                                @if($totalFloors > $maxFloorsToShow)
                                    <div class="flex items-center justify-center bg-gray-50 dark:bg-zinc-800 p-3 rounded-md text-sm text-gray-500 dark:text-gray-400">
                                        +{{ $totalFloors - $maxFloorsToShow }} more floors
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Units will be created with the specified details when you save the property</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                
                <!-- Amenities Section -->
                <div class="border-b border-gray-300 dark:border-zinc-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Amenities & Features</h2>
                    
                    <div class="mb-4">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="isPetsAllowed" class="rounded border-gray-300 dark:border-zinc-700 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Pets Allowed</span>
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($availableAmenities as $value => $label)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="amenities" value="{{ $value }}" class="rounded border-gray-300 dark:border-zinc-700 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <!-- Images Upload Section -->
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Property Image</h2>
                    
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-zinc-700 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="propertyImage" class="relative cursor-pointer bg-gray-50 dark:bg-zinc-800 rounded-md font-medium text-blue-600 dark:text-blue-500 hover:text-blue-500 dark:hover:text-blue-400 focus-within:outline-none">
                                    <span>Upload image</span>
                                    <input id="propertyImage" wire:model="propertyImages" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                PNG, JPG, GIF up to 5MB
                            </p>
                        </div>
                    </div>
                    
                    @error('propertyImages') 
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p> 
                    @enderror
                    
                    @if(!empty($propertyImages))
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Image:</h3>
                            <div class="relative w-full max-w-sm">
                                <img src="{{ $propertyImages[0]->temporaryUrl() }}" alt="Property image" class="w-full aspect-video object-cover rounded-lg">
                                <button type="button" wire:click="removeImage(0)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="inline-flex justify-center items-center rounded-md border border-transparent bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span wire:loading.remove wire:target="create">Create Property</span>
                            <span wire:loading wire:target="create" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                    </div>
                </form>
        </div>
    </div>
</div> 