<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ $mode === 'edit' ? 'Edit Rental' : 'Create Rental' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $mode === 'edit' ? 'Update rental information' : 'Create a new rental for a tenant' }}
            </p>
        </div>
        
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif
        
        @if (session('tenant_success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('tenant_success') }}
            </div>
        @endif
        
        @if (session('tenant_error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('tenant_error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form wire:submit="save">
                    <!-- Tenant Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tenant</label>
                        
                        @if($selectedTenant)
                            <!-- Selected Tenant Info -->
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded mb-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $selectedTenant->first_name }} {{ $selectedTenant->last_name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $selectedTenant->email }}</p>
                                        @if($selectedTenant->phone_number)
                                            <p class="text-sm text-gray-600">{{ $selectedTenant->phone_number }}</p>
                                        @endif
                                    </div>
                                    <button 
                                        type="button" 
                                        wire:click="selectTenant(null)" 
                                        class="text-sm text-red-600 hover:text-red-900"
                                    >
                                        Change
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Tenant Search -->
                            <div class="relative">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="tenantSearch" 
                                    placeholder="Search by name, email or phone number" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                
                                @if($searchResults && count($searchResults) > 0)
                                    <div class="absolute z-10 bg-white border rounded shadow-lg mt-1 w-full max-h-60 overflow-y-auto">
                                        @foreach($searchResults as $tenant)
                                            <div wire:click="selectTenant({{ $tenant->user_id }})" class="p-2 hover:bg-gray-100 cursor-pointer border-b">
                                                <div class="font-medium">{{ $tenant->first_name }} {{ $tenant->last_name }}</div>
                                                <div class="text-sm text-gray-600">{{ $tenant->email }}</div>
                                                @if($tenant->phone_number)
                                                    <div class="text-xs text-gray-500">{{ $tenant->phone_number }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-2">
                                <button 
                                    type="button" 
                                    wire:click="toggleNewTenantForm"
                                    class="text-sm text-indigo-600 hover:text-indigo-900"
                                >
                                    {{ $showNewTenantForm ? 'Cancel' : 'Create new tenant' }}
                                </button>
                            </div>
                            
                            @if($showNewTenantForm)
                                <div class="mt-3 p-4 bg-gray-50 border border-gray-200 rounded">
                                    <h3 class="font-medium mb-3">Create New Tenant</h3>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700">First Name</label>
                                        <input 
                                            type="text" 
                                            wire:model="newTenant.first_name" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        @error('newTenant.first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                        <input 
                                            type="text" 
                                            wire:model="newTenant.last_name" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        @error('newTenant.last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <input 
                                            type="email" 
                                            wire:model="newTenant.email" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        @error('newTenant.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                                        <input 
                                            type="text" 
                                            wire:model="newTenant.phone_number" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        @error('newTenant.phone_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button 
                                            type="button" 
                                            wire:click="createAndSelectTenant" 
                                            class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                        >
                                            Create & Select
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                        
                        <input type="hidden" wire:model="tenant_id">
                        @error('tenant_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Property Selection -->
                    <div class="mb-6">
                        <label for="property_id" class="block text-sm font-medium text-gray-700">Property</label>
                        <select 
                            wire:model.live="property_id" 
                            id="property_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                            <option value="">Select a property</option>
                            @foreach ($properties as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('property_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Unit Selection -->
                    <div class="mb-6">
                        <label for="room_id" class="block text-sm font-medium text-gray-700">Room/Unit</label>
                        <select 
                            wire:model="room_id" 
                            id="room_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            {{ empty($property_id) ? 'disabled' : '' }}
                        >
                            <option value="">Select a room</option>
                            @foreach ($units as $id => $roomNumber)
                                <option value="{{ $id }}">{{ $roomNumber }}</option>
                            @endforeach
                        </select>
                        @if (empty($units) && !empty($property_id))
                            <p class="mt-1 text-sm text-red-500">No available units for this property</p>
                        @endif
                        @error('room_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Start Date -->
                    <div class="mb-6">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input 
                            type="date" 
                            wire:model="start_date" 
                            id="start_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                        @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- End Date (Optional) -->
                    <div class="mb-6">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                        <input 
                            type="date" 
                            wire:model="end_date" 
                            id="end_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            min="{{ $start_date }}"
                        >
                        <p class="mt-1 text-sm text-gray-500">Leave empty for open-ended rental</p>
                        @error('end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Lease Agreement -->
                    <div class="mb-6">
                        <label for="lease_agreement" class="block text-sm font-medium text-gray-700">Lease Agreement (PDF, DOC, DOCX)</label>
                        <input 
                            type="file" 
                            wire:model="lease_agreement" 
                            id="lease_agreement"
                            accept=".pdf,.doc,.docx"
                            class="mt-1 block w-full border border-gray-300 shadow-sm rounded-md text-sm focus:outline-none"
                        >
                        @if ($existing_lease_agreement)
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a3 3 0 00-3-3 3 3 0 00-3 3v4a3 3 0 006 0V7a1 1 0 10-2 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                </svg>
                                <span>Current document: </span>
                                <a href="{{ Storage::url($existing_lease_agreement) }}" target="_blank" class="ml-1 text-indigo-600 hover:text-indigo-500">
                                    View document
                                </a>
                            </div>
                        @endif
                        <p class="mt-1 text-sm text-gray-500">Upload a copy of the signed lease agreement (max 10MB)</p>
                        @error('lease_agreement') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a 
                            href="{{ route('rentals.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.class="hidden" wire:target="save">Save</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 