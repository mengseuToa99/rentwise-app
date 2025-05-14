<div class="min-h-screen bg-gray-50 dark:bg-black">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        @if ($property)
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $property->property_name }}</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('properties.edit', $propertyId) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 dark:border-black rounded-md font-medium text-sm text-black dark:text-black shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Edit Property
                    </a>
                    <a href="{{ route('properties.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 dark:border-black rounded-md font-medium text-sm text-black dark:text-black shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Back to Properties
                    </a>
                </div>
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
            
            <!-- Property Details -->
            <div class="bg-gray-50 dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-300 dark:border-black mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Property Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $property->address }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $property->description }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Landlord</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $property->landlord->first_name }} {{ $property->landlord->last_name }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $property->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100' }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Floors</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $property->total_floors }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Units</h3>
                                <div class="flex space-x-4 mt-1">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">
                                        <span class="font-medium">Total:</span> 
                                        <span>{{ $totalUnits }}</span>
                                    </div>
                                    <div class="text-sm text-gray-900 dark:text-gray-200">
                                        <span class="font-medium">Occupied:</span> 
                                        <span>{{ $occupiedUnits }}</span>
                                    </div>
                                    <div class="text-sm text-gray-900 dark:text-gray-200">
                                        <span class="font-medium">Vacant:</span> 
                                        <span>{{ $vacantUnits }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Units -->
            <div class="bg-gray-50 dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-300 dark:border-black">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Units</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('pricing-groups.index', $propertyId) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 dark:border-black rounded-md font-medium text-xs text-black dark:text-black shadow-sm hover:bg-gray-50">
                                Manage Pricing Groups
                            </a>
                            <a href="{{ route('units.create') }}?property={{ $propertyId }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 dark:border-black rounded-md font-medium text-xs text-black dark:text-black shadow-sm hover:bg-gray-50">
                                Add Unit
                            </a>
                        </div>
                    </div>
                    
                    @if (count($units) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-zinc-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Number</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rent</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($units as $unit)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $unit['number'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $unit['type'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $unit['size'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">${{ number_format($unit['rent'], 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $unit['status'] === 'occupied' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' }}">
                                                    {{ ucfirst($unit['status']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <a href="{{ route('units.edit', $unit['id']) }}" class="text-gray-800 dark:text-white font-medium hover:underline underline-offset-4">Edit</a>
                                                    <button wire:click="deleteUnit({{ $unit['id'] }})" wire:confirm="Are you sure you want to delete this unit?" class="text-gray-800 dark:text-white font-medium hover:underline underline-offset-4">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-300">No units found for this property.</p>
                            <a href="{{ route('units.create') }}?property={{ $propertyId }}" class="mt-2 inline-block text-gray-800 dark:text-white font-medium hover:underline underline-offset-4">Add your first unit</a>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-gray-50 dark:bg-zinc-900 p-6 rounded-lg border border-gray-300 dark:border-black text-center">
                <p class="text-gray-500 dark:text-gray-300">Property not found or you don't have permission to view it.</p>
                <a href="{{ route('properties.index') }}" class="mt-4 inline-block text-gray-800 dark:text-white font-medium hover:underline underline-offset-4">Back to Properties</a>
            </div>
        @endif
    </div>
</div> 