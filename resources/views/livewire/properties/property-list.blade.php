<div class="min-h-screen bg-gray-50 dark:bg-black">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Properties</h1>
                         <a href="{{ route('properties.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 dark:border-black rounded-md font-medium text-sm text-black dark:text-black shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Add Property
            </a>
        </div>
        
        @if (session('success'))
            <div class="mb-4 p-3 text-sm text-green-600 bg-green-100/40 dark:bg-green-900/20 dark:text-green-400 rounded-md">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="mb-4 p-3 text-sm text-red-600 bg-red-100/40 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="bg-gray-50 dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-300 dark:border-black mb-6">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-1">
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="Search properties..." 
                            class="block w-full rounded-md bg-transparent border border-gray-300 dark:border-black px-4 py-3 text-md shadow-none placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                        >
                    </div>
                </div>
            </div>
        </div>
        
        @if ($properties->isEmpty())
            <div class="bg-gray-50 dark:bg-zinc-900 p-6 rounded-lg border border-gray-300 dark:border-black text-center">
                <p class="text-gray-500 dark:text-gray-300">No properties found.</p>
                <a href="{{ route('properties.create') }}" class="mt-4 inline-block text-gray-800 dark:text-white font-medium hover:underline underline-offset-4">Create your first property</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($properties as $property)
                    <div class="bg-gray-50 dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-300 dark:border-black">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">{{ $property->property_name }}</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $property->address }}</p>
                            
                            <div class="flex justify-between mb-4">
                                <div class="text-sm text-gray-700 dark:text-gray-200">
                                    <span class="font-medium">Units:</span> 
                                    <span class="ml-1">{{ $property->total_units }}</span>
                                </div>
                                <div class="text-sm text-gray-700 dark:text-gray-200">
                                    <span class="font-medium">Occupied:</span> 
                                    <span class="ml-1">{{ $property->occupied_units }}</span>
                                </div>
                                <div class="text-sm text-gray-700 dark:text-gray-200">
                                    <span class="font-medium">Vacant:</span> 
                                    <span class="ml-1">{{ $property->total_units - $property->occupied_units }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-4">
                                <a href="{{ route('properties.show', $property->property_id) }}" class="text-gray-800 dark:text-white font-medium hover:underline underline-offset-4">View Details</a>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('properties.edit', $property->property_id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 dark:border-black rounded-md font-medium text-xs text-black dark:text-black shadow-sm hover:bg-gray-50">
                                        Edit
                                    </a>
                                    <button 
                                        wire:click="deleteProperty({{ $property->property_id }})"
                                        wire:confirm="Are you sure you want to delete this property? All associated units will also be deleted."
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 dark:border-black rounded-md font-medium text-xs text-black dark:text-black shadow-sm hover:bg-gray-50"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6 text-gray-900 dark:text-white">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</div> 