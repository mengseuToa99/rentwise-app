<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Maintenance Request Details</h2>
            <p class="mt-1 text-sm text-gray-600">Update the status and add notes for this maintenance request.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Request Details (Read-only) -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Property Details</h3>
                <div class="mt-2 text-sm text-gray-600">
                    <p><strong>Property:</strong> {{ $maintenanceRequest->property->property_name }}</p>
                    <p><strong>Unit:</strong> {{ $maintenanceRequest->room->room_number }}</p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Request Information</h3>
                <div class="mt-2 text-sm text-gray-600">
                    <p><strong>Title:</strong> {{ $maintenanceRequest->title }}</p>
                    <p><strong>Priority:</strong> {{ ucfirst($maintenanceRequest->priority) }}</p>
                    <p><strong>Submitted:</strong> {{ $maintenanceRequest->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900">Description</h3>
            <div class="mt-2 text-sm text-gray-600">
                {{ $maintenanceRequest->description }}
            </div>
        </div>

        <form wire:submit.prevent="updateStatus" class="space-y-6">
            <!-- Status Update -->
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
                <label for="landlord_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea id="landlord_notes" wire:model="landlord_notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add any notes about the maintenance work..."></textarea>
                @error('landlord_notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('maintenance.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Back to List
                </a>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div> 