<div class="py-4 bg-gray-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if ($property)
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <div class="flex items-center">
                        <a href="{{ route('properties.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $property->property_name }}</h1>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        @php
                            $addressParts = [];
                            if($property->house_building_number) $addressParts[] = $property->house_building_number;
                            if($property->street) $addressParts[] = $property->street;
                            if($property->village) $addressParts[] = $property->village;
                            if($property->commune) $addressParts[] = $property->commune;
                            if($property->district) $addressParts[] = $property->district;
                        @endphp
                        {{ implode(', ', $addressParts) }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('properties.edit', $propertyId) }}" class="inline-flex items-center px-3 py-2 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Property
                    </a>
                    <a href="{{ route('units.create') }}?property={{ $propertyId }}" class="inline-flex items-center px-3 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Unit
                    </a>
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
            
            <!-- Property Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $property->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                            {{ ucfirst($property->status) }}
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ ucfirst($property->status) }}
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Current property status</p>
                </div>
                    
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm p-4">
                    <div class="flex items-center mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Units</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $totalUnits }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <span class="text-green-600 dark:text-green-400 font-medium">{{ $occupiedUnits }}</span> occupied, 
                        <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $vacantUnits }}</span> vacant
                    </div>
                            </div>
                            
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm p-4">
                    <div class="flex items-center mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Occupancy Rate</span>
                    </div>
                    @php
                        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;
                    @endphp
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $occupancyRate }}%
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-1.5">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $occupancyRate }}%"></div>
                    </div>
                            </div>
                            
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm p-4">
                    <div class="flex items-center mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Property Size</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $property->total_floors }} floors
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total floors in the building</p>
                            </div>
                        </div>
                        
            <!-- Property Details -->
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <!-- Property Image Header -->
                    <div class="w-full h-48 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 relative">
                        @if(!empty($propertyImages))
                            <img src="{{ Storage::url($propertyImages[0]) }}" alt="{{ $property->property_name }}" class="w-full h-full object-cover">
                        @endif
                    </div>

                    <div class="border-b border-gray-200 dark:border-zinc-800 px-4 py-3">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">Property Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Description Section -->
                            <div class="lg:col-span-2 bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-6">
                                <div class="flex items-center gap-2 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Description</h3>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    {{ $property->description ?: 'No description provided' }}
                                </p>
                            </div>

                            <!-- Quick Info Section -->
                            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-6">
                                <div class="flex items-center gap-2 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Quick Info</h3>
                                </div>
                                <div class="space-y-4">
                                    <!-- Landlord Info -->
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Landlord</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $property->landlord->first_name }} {{ $property->landlord->last_name }}</p>
                                        </div>
                                    </div>

                                    <!-- Property Type -->
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Property Type</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ ucfirst($property->property_type) }}</p>
                                        </div>
                                    </div>

                                    <!-- Year Built -->
                                    @if($property->year_built)
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Year Built</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $property->year_built }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="lg:col-span-3 bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-6">
                                <div class="flex items-center gap-2 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Address Details</h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @if($property->house_building_number)
                                    <div class="flex items-start gap-3">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">House/Building Number</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $property->house_building_number }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">ផ្ទះលេខ/អគារលេខ</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($property->street)
                                    <div class="flex items-start gap-3">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Street</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $property->street }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">ផ្លូវ</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($property->village)
                                    <div class="flex items-start gap-3">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Village</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $property->village }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">ភូមិ</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($property->commune)
                                    <div class="flex items-start gap-3">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Commune/Sangkat</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $property->commune }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">សង្កាត់/ឃុំ</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($property->district)
                                    <div class="flex items-start gap-3">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">District/Khan</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $property->district }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">ខណ្ឌ/ស្រុក</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Units Section -->
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-zinc-800 px-4 py-3 flex justify-between items-center">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">Units</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('pricing-groups.index', $propertyId) }}" class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-zinc-800 rounded text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                </svg>
                                Pricing Groups
                            </a>
                        </div>
                    </div>
                    
                    @if (count($units) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                <thead class="bg-gray-50 dark:bg-zinc-800">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Size</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rent</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach ($units as $unit)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $unit['number'] }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $unit['type'] }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $unit['size'] }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${{ number_format($unit['rent'], 2) }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $unit['status'] === 'occupied' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' }}">
                                                    {{ ucfirst($unit['status']) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <a href="{{ route('units.edit', $unit['id']) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">Edit</a>
                                                    <button wire:click="deleteUnit({{ $unit['id'] }})" wire:confirm="Are you sure you want to delete this unit?" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-6 px-4">
                            <div class="rounded-full bg-gray-100 dark:bg-zinc-800 p-3 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-3">No units found for this property</p>
                            <a href="{{ route('units.create') }}?property={{ $propertyId }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md text-xs font-medium text-white shadow-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add your first unit
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center bg-white dark:bg-zinc-900 p-8 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm">
                <div class="rounded-full bg-gray-100 dark:bg-zinc-800 p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Property Not Found</h3>
                <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm mb-4">The property you're looking for may not exist or you don't have permission to view it.</p>
                <a href="{{ route('properties.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Properties
                </a>
            </div>
        @endif
    </div>
</div> 