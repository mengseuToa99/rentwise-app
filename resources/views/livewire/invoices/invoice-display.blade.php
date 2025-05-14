<div class="py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white mb-2 sm:mb-0">
                Invoice Details
            </h1>
            <a 
                href="{{ route('tenant.invoices') }}" 
                wire:navigate
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Back to Invoices
            </a>
        </div>

        <!-- Loading State -->
        <div wire:loading class="w-full text-center py-8">
            <div class="inline-block animate-spin h-8 w-8 border-4 border-indigo-500 rounded-full border-t-transparent"></div>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Loading invoice...</p>
        </div>

        <!-- Invoice Card Container -->
        <div wire:loading.remove class="w-full mx-auto perspective">
            <div 
                class="relative w-full h-full transform transition-all duration-700 ease-in-out {{ $isFlipped ? 'rotate-y-180' : '' }}"
                style="transform-style: preserve-3d;"
                aria-live="polite"
            >
                <!-- Front of Card (Summary) -->
                <div 
                    class="absolute w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sm:p-8 backface-hidden border border-gray-200 dark:border-gray-700"
                    aria-hidden="{{ $isFlipped ? 'true' : 'false' }}"
                >
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                Invoice #INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Issued: {{ $invoice->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        
                        @php
                            $statusColors = [
                                'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            ];
                            $statusColor = $statusColors[$invoice->payment_status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        @endphp
                        
                        <span class="mt-2 sm:mt-0 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                            {{ ucfirst($invoice->payment_status) }}
                        </span>
                    </div>
                    
                    <div class="border-t border-gray-200 dark:border-gray-700 py-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Tenant:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $invoice->rental->tenant->full_name ?? $invoice->rental->tenant->name }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Property:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $invoice->rental->unit->property->name }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Unit/Room:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $invoice->rental->unit->room_number }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Due Date:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                        <div class="flex justify-between items-center text-lg font-bold">
                            <span class="text-gray-900 dark:text-white">Total Amount:</span>
                            <span class="text-gray-900 dark:text-white">${{ number_format($invoice->amount_due, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-center">
                        <button 
                            wire:click="toggleFlip"
                            class="inline-flex items-center justify-center min-h-[48px] min-w-[48px] px-6 py-3 bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md shadow-sm text-base font-medium text-white transition-colors duration-200"
                            aria-label="View invoice details"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            View Details
                        </button>
                    </div>
                </div>
                
                <!-- Back of Card (Details) -->
                <div 
                    class="absolute w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sm:p-8 backface-hidden rotate-y-180 border border-gray-200 dark:border-gray-700"
                    aria-hidden="{{ $isFlipped ? 'false' : 'true' }}"
                >
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                Invoice Details
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Invoice #INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Line Items Section -->
                    <div class="border rounded-md p-4 mb-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Line Items</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider py-2">Description</th>
                                        <th class="text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider py-2">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-3 text-sm text-gray-900 dark:text-white">Monthly Rent</td>
                                        <td class="py-3 text-sm text-gray-900 dark:text-white text-right">${{ number_format($invoice->amount_due, 2) }}</td>
                                    </tr>
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="py-3 text-sm font-bold text-gray-900 dark:text-white">Total</td>
                                        <td class="py-3 text-sm font-bold text-gray-900 dark:text-white text-right">${{ number_format($invoice->amount_due, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Payment Terms Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border rounded-md p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Payment Terms</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Due Date:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Payment Method:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $invoice->payment_method ?? 'Not specified' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Late Fee:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">Per rental agreement</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border rounded-md p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Property Details</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Property:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $invoice->rental->unit->property->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Unit/Room:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $invoice->rental->unit->room_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Address:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $invoice->rental->unit->property->address ?? 'Not available' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-center">
                        <button 
                            wire:click="toggleFlip"
                            class="inline-flex items-center justify-center min-h-[48px] min-w-[48px] px-6 py-3 bg-gray-600 hover:bg-gray-700 focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 rounded-md shadow-sm text-base font-medium text-white transition-colors duration-200"
                            aria-label="Back to invoice summary"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .perspective {
            perspective: 1500px;
        }
        
        .backface-hidden {
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
        
        .rotate-y-180 {
            transform: rotateY(180deg);
        }
        
        /* For users who prefer reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .rotate-y-180, [wire\:click="toggleFlip"] {
                transition: none !important;
            }
        }
    </style>
</div>
