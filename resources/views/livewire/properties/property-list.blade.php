<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Properties</h1>
            <a href="{{ route('properties.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Add Property
            </a>
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
        
        <div class="bg-white overflow-hidden 0">
                <div class="flex items-center">
                    <div class="flex-1">
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholdshadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-20er="Search properties..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-10 p-4"  
                        >
                    </div>
                </div>
            </div>
        </div>
        
        @if ($properties->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <p class="text-gray-500">No properties found.</p>
                <a href="{{ route('properties.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">Create your first property</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($properties as $property)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-semibold mb-2">{{ $property->property_name }}</h2>
                            <p class="text-gray-600 mb-4">{{ $property->address }}</p>
                            
                            <div class="flex justify-between mb-4">
                                <div class="text-sm">
                                    <span class="font-medium">Units:</span> 
                                    <span class="ml-1">{{ $property->total_units }}</span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium">Occupied:</span> 
                                    <span class="ml-1">{{ $property->occupied_units }}</span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium">Vacant:</span> 
                                    <span class="ml-1">{{ $property->total_units - $property->occupied_units }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-4">
                                <a href="{{ route('properties.show', $property->property_id) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('properties.edit', $property->property_id) }}" class="inline-flex items-center px-3 py-1 bg-yellow-500 border border-transparent rounded-md font-medium text-xs text-white hover:bg-yellow-600">
                                        Edit
                                    </a>
                                    <button 
                                        wire:click="deleteProperty({{ $property->property_id }})"
                                        wire:confirm="Are you sure you want to delete this property? All associated units will also be deleted."
                                        class="inline-flex items-center px-3 py-1 bg-red-500 border border-transparent rounded-md font-medium text-xs text-white hover:bg-red-600"
                                    >
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