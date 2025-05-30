<div class="py-4 bg-gray-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-400 dark:text-red-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-400 dark:text-green-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">My Properties</h1>
            <div class="flex items-center gap-2">
                <div class="relative rounded-md shadow-sm flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="Search properties..." 
                        class="block w-full pl-10 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 py-2 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                </div>
                <a href="{{ route('properties.create') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Property
                </a>
            </div>
        </div>
        
        @if ($properties->isEmpty())
            <div class="flex flex-col items-center justify-center bg-white dark:bg-zinc-900 p-8 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm">
                <div class="rounded-full bg-gray-100 dark:bg-zinc-800 p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No properties found</h3>
                <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm mb-4">Add your first property to start managing your real estate portfolio and track rental income.</p>
                <a href="{{ route('properties.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create your first property
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($properties as $property)
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all overflow-hidden">
                        <div class="h-32 bg-gradient-to-r from-blue-500 to-indigo-600 relative">
                            <div class="absolute inset-0 bg-black/10"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                <h2 class="text-lg font-medium text-white">{{ $property->property_name }}</h2>
                                <p class="text-sm text-white/80 truncate">
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
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Property Stats</div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $property->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <div class="bg-gray-50 dark:bg-zinc-800 rounded p-2 text-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Units</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $property->total_units }}</span>
                                </div>
                                <div class="bg-gray-50 dark:bg-zinc-800 rounded p-2 text-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Occupied</span>
                                    <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $property->occupied_units }}</span>
                                </div>
                                <div class="bg-gray-50 dark:bg-zinc-800 rounded p-2 text-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Vacant</span>
                                    <span class="text-lg font-semibold text-amber-600 dark:text-amber-400">{{ $property->total_units - $property->occupied_units }}</span>
                                </div>
                            </div>
                            
                            <div class="h-1.5 w-full bg-gray-200 dark:bg-zinc-800 rounded-full mb-4">
                                @php
                                    $occupancyRate = $property->total_units > 0 ? ($property->occupied_units / $property->total_units) * 100 : 0;
                                @endphp
                                <div class="h-1.5 bg-blue-600 rounded-full" style="width: {{ $occupancyRate }}%"></div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <a href="{{ route('properties.show', $property->property_id) }}" class="text-blue-600 dark:text-blue-400 text-sm font-medium hover:underline">
                                    View Details
                                </a>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('properties.edit', $property->property_id) }}" class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-zinc-800 rounded text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <button 
                                        wire:click="deleteProperty({{ $property->property_id }})"
                                        wire:confirm="Are you sure you want to delete this property? All associated units will also be deleted."
                                        class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-zinc-800 rounded text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</div> 