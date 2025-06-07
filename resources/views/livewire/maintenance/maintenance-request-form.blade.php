<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $mode === 'edit' ? 'Edit Maintenance Request' : 'Create Maintenance Request' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $mode === 'edit' ? 'Update maintenance request details' : 'Submit a new maintenance request' }}
            </p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-md border border-green-200 dark:border-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-md border border-red-200 dark:border-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="p-6 bg-white rounded-lg shadow-md">
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Property Selection -->
                    <div>
                        <label for="selectedProperty" class="block text-sm font-medium text-gray-700">Property</label>
                        <select id="selectedProperty" wire:model="selectedProperty" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isLandlord && $mode === 'edit' ? 'disabled' : '' }}>
                            <option value="">Select a property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->property_id }}">{{ $property->property_name }}</option>
                            @endforeach
                        </select>
                        @error('selectedProperty') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Unit Selection -->
                    <div>
                        <label for="selectedUnit" class="block text-sm font-medium text-gray-700">Unit</label>
                        <select id="selectedUnit" wire:model="selectedUnit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isLandlord && $mode === 'edit' ? 'disabled' : '' }}>
                            <option value="">Select a unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->room_id }}">{{ $unit->room_number }}</option>
                            @endforeach
                        </select>
                        @error('selectedUnit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="title" wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isLandlord ? 'disabled' : '' }}>
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" wire:model="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isLandlord ? 'disabled' : '' }}></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                    <select id="priority" wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isLandlord ? 'disabled' : '' }}>
                        @foreach($priorities as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('priority') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @if($mode === 'edit')
                    <!-- Status (only visible in edit mode) -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Landlord Notes -->
                    <div>
                        <label for="landlord_notes" class="block text-sm font-medium text-gray-700">Landlord Notes</label>
                        <textarea id="landlord_notes" wire:model="landlord_notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('landlord_notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('maintenance.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        {{ $mode === 'create' ? 'Create Request' : 'Update Request' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 