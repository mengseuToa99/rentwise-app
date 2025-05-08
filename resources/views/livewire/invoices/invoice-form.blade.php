<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ $mode === 'edit' ? 'Edit Invoice' : 'Create Invoice' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $mode === 'edit' ? 'Update invoice information' : 'Generate a new invoice for a rental' }}
            </p>
        </div>
        
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form wire:submit="save">
                    <!-- Rental Selection -->
                    <div class="mb-6">
                        <label for="rental_id" class="block text-sm font-medium text-gray-700">Rental</label>
                        <select 
                            wire:model="rental_id" 
                            id="rental_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                            <option value="">Select a rental</option>
                            @foreach ($rentals as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('rental_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Amount Due -->
                    <div class="mb-6">
                        <label for="amount_due" class="block text-sm font-medium text-gray-700">Amount Due</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input 
                                type="number" 
                                wire:model="amount_due" 
                                id="amount_due" 
                                class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                            >
                        </div>
                        @error('amount_due') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Due Date -->
                    <div class="mb-6">
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input 
                            type="date" 
                            wire:model="due_date" 
                            id="due_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                        @error('due_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Payment Status Section -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-md">
                        <h3 class="text-md font-medium text-gray-700 mb-3">Payment Information</h3>
                        
                        <!-- Paid Toggle -->
                        <div class="mb-4">
                            <label for="paid" class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="paid" 
                                    id="paid"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                <span class="ml-2 text-sm text-gray-700">Mark as paid</span>
                            </label>
                            @error('paid') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="mb-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select 
                                wire:model="payment_method" 
                                id="payment_method"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                {{ !$paid ? 'disabled' : '' }}
                            >
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                            @error('payment_method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                            <select 
                                wire:model.live="payment_status" 
                                id="payment_status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                            @error('payment_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a 
                            href="{{ route('invoices.index') }}" 
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