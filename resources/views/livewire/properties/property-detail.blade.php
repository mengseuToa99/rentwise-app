<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        @if ($property)
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">{{ $property->property_name }}</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('properties.edit', $propertyId) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                        Edit Property
                    </a>
                    <a href="{{ route('properties.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Back to Properties
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
            
            <!-- Property Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-xl font-medium text-gray-900 mb-4">Property Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Address</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $property->address }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Description</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $property->description }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Landlord</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $property->landlord->first_name }} {{ $property->landlord->last_name }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $property->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Total Floors</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $property->total_floors }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Units</h3>
                                <div class="flex space-x-4 mt-1">
                                    <div class="text-sm">
                                        <span class="font-medium">Total:</span> 
                                        <span>{{ $totalUnits }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="font-medium">Occupied:</span> 
                                        <span>{{ $occupiedUnits }}</span>
                                    </div>
                                    <div class="text-sm">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-medium text-gray-900">Units</h2>
                        <a href="{{ route('units.create') }}?property={{ $propertyId }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Add Unit
                        </a>
                    </div>
                    
                    @if (count($units) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Number</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rent</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($units as $unit)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $unit['number'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $unit['type'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $unit['size'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($unit['rent'], 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $unit['status'] === 'occupied' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ ucfirst($unit['status']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <a href="{{ route('units.edit', $unit['id']) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    <button wire:click="deleteUnit({{ $unit['id'] }})" wire:confirm="Are you sure you want to delete this unit?" class="text-red-600 hover:text-red-900">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">No units found for this property.</p>
                            <a href="{{ route('units.create') }}?property={{ $propertyId }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-800">Add your first unit</a>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <p class="text-gray-500">Property not found or you don't have permission to view it.</p>
                <a href="{{ route('properties.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">Back to Properties</a>
            </div>
        @endif
    </div>
</div> 