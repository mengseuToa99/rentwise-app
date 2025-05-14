<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Pricing Groups</h1>
                <p class="text-gray-600">Property: {{ $property->property_name }}</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('pricing-groups.create', $property->property_id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Add Pricing Group
                </a>
                <a href="{{ route('properties.show', $property->property_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Property
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
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-1">
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="Search pricing groups..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                    </div>
                </div>
            </div>
        </div>
        
        @if ($pricingGroups->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <p class="text-gray-500">No pricing groups found for this property.</p>
                <a href="{{ route('pricing-groups.create', $property->property_id) }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">Create your first pricing group</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($pricingGroups as $pricingGroup)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <h2 class="text-xl font-semibold">{{ $pricingGroup->group_name }}</h2>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pricingGroup->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($pricingGroup->status) }}
                                </span>
                            </div>
                            
                            <p class="text-gray-600 mb-2">Room Type: {{ $pricingGroup->room_type }}</p>
                            <p class="text-gray-600 mb-4">Base Price: ${{ number_format($pricingGroup->base_price, 2) }}</p>
                            
                            @if($pricingGroup->description)
                                <p class="text-gray-500 text-sm mb-3">{{ \Illuminate\Support\Str::limit($pricingGroup->description, 100) }}</p>
                            @endif
                            
                            <div class="flex items-center justify-between mt-4">
                                <div class="text-sm">
                                    <span class="font-medium">Units:</span> 
                                    <span class="ml-1">{{ $pricingGroup->units_count }}</span>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('pricing-groups.edit', ['property' => $property->property_id, 'group' => $pricingGroup->group_id]) }}" class="inline-flex items-center px-3 py-1 bg-yellow-500 border border-transparent rounded-md font-medium text-xs text-white hover:bg-yellow-600">
                                        Edit
                                    </a>
                                    <button 
                                        wire:click="deletePricingGroup({{ $pricingGroup->group_id }})"
                                        wire:confirm="Are you sure you want to delete this pricing group? The pricing information for all associated units will be reset."
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
                {{ $pricingGroups->links() }}
            </div>
        @endif
    </div>
</div>
