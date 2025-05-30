<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $mode === 'edit' ? 'Edit Rental' : 'Create Rental' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $mode === 'edit' ? 'Update rental information' : 'Create a new rental for a tenant' }}
            </p>
        </div>
        
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-md border border-red-200 dark:border-red-800">
                {{ session('error') }}
            </div>
        @endif
        
        @if (session('tenant_success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-md border border-green-200 dark:border-green-800">
                {{ session('tenant_success') }}
            </div>
        @endif
        
        @if (session('tenant_error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-md border border-red-200 dark:border-red-800">
                {{ session('tenant_error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800">
                <form wire:submit="save">
                    <!-- Tenant Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant</label>
                        
                        @if($selectedTenant)
                            <!-- Selected Tenant Info -->
                            <div class="p-4 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded mb-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-white">{{ $selectedTenant->first_name }} {{ $selectedTenant->last_name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedTenant->email }}</p>
                                        @if($selectedTenant->phone_number)
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedTenant->phone_number }}</p>
                                        @endif
                                    </div>
                                    <button 
                                        type="button" 
                                        wire:click="selectTenant(null)" 
                                        class="text-sm text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
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
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 py-2.5 text-base px-4"
                                >
                                
                                @if($searchResults && count($searchResults) > 0)
                                    <div class="absolute z-10 bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded shadow-lg mt-1 w-full max-h-60 overflow-y-auto">
                                        @foreach($searchResults as $tenant)
                                            <div wire:click="selectTenant({{ $tenant->user_id }})" class="p-2 hover:bg-gray-100 dark:hover:bg-zinc-700 cursor-pointer border-b dark:border-zinc-700">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $tenant->first_name }} {{ $tenant->last_name }}</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $tenant->email }}</div>
                                                @if($tenant->phone_number)
                                                    <div class="text-xs text-gray-500 dark:text-gray-500">{{ $tenant->phone_number }}</div>
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
                                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                >
                                    {{ $showNewTenantForm ? 'Cancel' : 'Create new tenant' }}
                                </button>
                            </div>
                            
                            @if($showNewTenantForm)
                                <div class="mt-3 p-4 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded">
                                    <h3 class="font-medium mb-3 text-gray-900 dark:text-white">Create New Tenant</h3>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                                        <input 
                                            type="text" 
                                            wire:model="newTenant.first_name" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 py-2.5 text-base px-4"
                                        >
                                        @error('newTenant.first_name') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                                        <input 
                                            type="text" 
                                            wire:model="newTenant.last_name" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 py-2.5 text-base px-4"
                                        >
                                        @error('newTenant.last_name') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <input 
                                            type="email" 
                                            wire:model="newTenant.email" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 py-2.5 text-base px-4"
                                        >
                                        @error('newTenant.email') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                        <input 
                                            type="text" 
                                            wire:model="newTenant.phone_number" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 py-2.5 text-base px-4"
                                        >
                                        @error('newTenant.phone_number') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button 
                                            type="button" 
                                            wire:click="createAndSelectTenant" 
                                            class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition"
                                        >
                                            Create & Select
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                        
                        <input type="hidden" wire:model="tenant_id">
                        @error('tenant_id') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Property Selection -->
                    <div class="mb-6">
                        <label for="property_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Property</label>
                        <select 
                            wire:model.live="property_id" 
                            id="property_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 py-2.5 text-base px-4 pr-10"
                        >
                            <option value="">Select a property</option>
                            @foreach ($properties as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('property_id') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Unit Selection -->
                    <div class="mb-6">
                        <label for="room_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room/Unit</label>
                        <select 
                            wire:model="room_id" 
                            id="room_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 py-2.5 text-base px-4 pr-10"
                            {{ empty($property_id) ? 'disabled' : '' }}
                        >
                            <option value="">Select a room</option>
                            @foreach ($units as $id => $roomNumber)
                                <option value="{{ $id }}">{{ $roomNumber }}</option>
                            @endforeach
                        </select>
                        @if (empty($units) && !empty($property_id))
                            <p class="mt-1 text-sm text-red-500 dark:text-red-400">No available units for this property</p>
                        @endif
                        @error('room_id') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Start Date -->
                    <div class="mb-6">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                        <input 
                            type="date" 
                            wire:model="start_date" 
                            id="start_date"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 py-2.5 text-base px-4"
                        >
                        @error('start_date') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- End Date (Optional) -->
                    <div class="mb-6">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date (Optional)</label>
                        <input 
                            type="date" 
                            wire:model="end_date" 
                            id="end_date"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 py-2.5 text-base px-4"
                            min="{{ $start_date }}"
                        >
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave empty for open-ended rental</p>
                        @error('end_date') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Lease Agreement -->
                    <div class="mb-6">
                        <label for="lease_agreement" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lease Agreement (Optional)</label>
                        <input 
                            type="file" 
                            wire:model="lease_agreement" 
                            id="lease_agreement"
                            class="mt-1 block w-full text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 dark:file:bg-zinc-800 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-zinc-700"
                            accept=".pdf,.doc,.docx"
                        >
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload PDF, DOC, or DOCX file (max 10MB)</p>
                        @if($existing_lease_agreement)
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Current file:</span>
                                <a href="{{ Storage::url($existing_lease_agreement) }}" target="_blank" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">View</a>
                            </div>
                        @endif
                        @error('lease_agreement') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a 
                            href="{{ route('rentals.index') }}" 
                            class="inline-flex items-center px-4 py-2.5 bg-gray-200 dark:bg-zinc-700 border border-transparent rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-zinc-600 active:bg-gray-400 dark:active:bg-zinc-700 focus:outline-none focus:border-gray-500 dark:focus:border-zinc-600 focus:ring ring-gray-300 dark:ring-zinc-600 disabled:opacity-25 transition"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition"
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