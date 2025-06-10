<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Maintenance Request Details</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update the status and add notes for this maintenance request.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Request Details (Read-only) -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Property Details</h3>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <p><strong>Property:</strong> {{ $maintenanceRequest->property->property_name }}</p>
                    <p><strong>Unit:</strong> {{ $maintenanceRequest->room->room_number }}</p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Request Information</h3>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <p><strong>Title:</strong> {{ $maintenanceRequest->title }}</p>
                    <p>
                        <strong>Priority:</strong>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @switch($maintenanceRequest->priority)
                                @case('urgent')
                                    bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400
                                    @break
                                @case('high')
                                    bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-400
                                    @break
                                @case('medium')
                                    bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400
                                    @break
                                @default
                                    bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400
                            @endswitch
                        ">
                            {{ ucfirst($maintenanceRequest->priority) }}
                        </span>
                    </p>
                    <p><strong>Submitted:</strong> {{ $maintenanceRequest->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Description</h3>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-zinc-900/50 p-4 rounded-md">
                {{ $maintenanceRequest->description }}
            </div>
        </div>

        <!-- Quick Actions -->
        @if($maintenanceRequest->status === 'pending')
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Quick Actions</h3>
            <div class="flex space-x-3">
                <button wire:click="quickAction('in_progress')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Accept Request
                </button>
                <button wire:click="quickAction('rejected')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Reject Request
                </button>
            </div>
        </div>
        @endif

        <form wire:submit.prevent="updateStatus" class="space-y-6">
            <!-- Status Update -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <div class="mt-1">
                    <select id="status" wire:model="status" class="block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:text-white">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @if($value === $status) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @error('status') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <!-- Landlord Notes -->
            <div>
                <label for="landlord_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                <div class="mt-1">
                    <textarea 
                        id="landlord_notes" 
                        wire:model="landlord_notes" 
                        rows="4" 
                        class="block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:text-white" 
                        placeholder="Add any notes about the maintenance work..."
                    ></textarea>
                </div>
                @error('landlord_notes') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('maintenance.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Back to List
                </a>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div> 