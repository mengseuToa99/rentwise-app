<div class="py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white mb-2 sm:mb-0">
                @if($invoice)
                Invoice Details
                @else
                    Invoices
                @endif
            </h1>
            <div class="flex space-x-3">
                @if(!$invoice)
                    <a 
                        href="{{ route('invoices.create') }}" 
                        class="inline-flex items-center px-3 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Invoice
                    </a>
                @endif
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
        </div>
        
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-4 p-3 flex items-center text-sm text-green-600 bg-green-100 dark:bg-green-900/20 dark:text-green-400 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="mb-4 p-3 flex items-center text-sm text-red-600 bg-red-100 dark:bg-red-900/20 dark:text-red-400 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Loading State -->
        <div wire:loading class="w-full text-center py-8">
            <div class="inline-block animate-spin h-8 w-8 border-4 border-indigo-500 rounded-full border-t-transparent"></div>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Loading invoice...</p>
        </div>

        <!-- Filters & Search Bar -->
        <div wire:loading.remove class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sm:p-8 mb-6 border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            id="search"
                            placeholder="Search invoices..." 
                            class="block w-full pl-10 py-2 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                    </div>
                </div>
                
                <div>
                    <label for="statusFilter" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Payment Status</label>
                    <select 
                        wire:model.live="statusFilter" 
                        id="statusFilter" 
                        class="block w-full py-2 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
                
                <div>
                    <label for="rentalFilter" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tenant</label>
                    <select 
                        wire:model.live="rentalFilter" 
                        id="rentalFilter" 
                        class="block w-full py-2 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="">All Tenants</option>
                        @foreach ($rentals as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Due Date Range</label>
                    <div class="flex space-x-2">
                        <input 
                            type="date" 
                            wire:model.live="dateFrom"
                            class="block w-full py-2 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="From"
                        >
                        <input 
                            type="date" 
                            wire:model.live="dateTo"
                            class="block w-full py-2 rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="To"
                        >
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end mt-3 border-t dark:border-gray-700 pt-3">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Rows per page:</span>
                    <select wire:model.live="perPage" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md text-sm py-1 px-3 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($paginationOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Invoice Card Container -->
        <!-- If we're not viewing a specific invoice, show the invoices list -->
        @if(!$invoice && isset($invoices) && count($invoices) > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice #</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tenant</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property & Room</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($invoices as $invoice_item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('tenant.invoice.view', $invoice_item->invoice_id) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                            INV-{{ str_pad($invoice_item->invoice_id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $invoice_item->tenant_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $invoice_item->property_name ?? 'N/A' }} ({{ $invoice_item->room_number ?? 'N/A' }})
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        ${{ number_format($invoice_item->amount_due, 2) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($invoice_item->due_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $statusClass = match($invoice_item->payment_status) {
                                                'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ ucfirst($invoice_item->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            @if($invoice_item->payment_status !== 'paid')
                                                <button 
                                                    wire:click="markAsPaid({{ $invoice_item->invoice_id }})" 
                                                    wire:confirm="Are you sure you want to mark this invoice as paid?"
                                                    class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-xs"
                                                >
                                                    Mark Paid
                                                </button>
                                            @endif
                                            <a href="{{ route('tenant.invoice.view', $invoice_item->invoice_id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">View</a>
                                            <button wire:click="deleteInvoice({{ $invoice_item->invoice_id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                @if($perPage !== 'all')
                    {{ $invoices->links() }}
                @else
                    <div class="text-sm text-gray-600 dark:text-gray-400 text-center">
                        Showing all {{ $invoices->count() }} results
                    </div>
                @endif
            </div>
        @elseif(!$invoice && isset($invoices) && count($invoices) === 0)
            <div class="flex flex-col items-center justify-center bg-white dark:bg-gray-800 p-8 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="rounded-full bg-gray-100 dark:bg-gray-700 p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No invoices found</h3>
                <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm mb-4">
                    No invoices found matching your criteria.
                </p>
            </div>
        @elseif($invoice)
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
                    
                    <div class="mt-8 flex flex-col sm:flex-row justify-between gap-4">
                        <div class="flex justify-center">
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
                        
                        <div class="flex justify-center space-x-3">
                            @if($invoice->payment_status !== 'paid')
                                <button 
                                    wire:click="markAsPaid({{ $invoice->invoice_id }})" 
                                    wire:confirm="Are you sure you want to mark this invoice as paid?"
                                    class="inline-flex items-center justify-center min-h-[48px] min-w-[48px] px-6 py-3 bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-offset-2 focus:ring-green-500 rounded-md shadow-sm text-base font-medium text-white transition-colors duration-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Mark as Paid
                                </button>
                            @endif
                            
                            <button 
                                wire:click="deleteInvoice({{ $invoice->invoice_id }})" 
                                wire:confirm="Are you sure you want to delete this invoice? This action cannot be undone."
                                class="inline-flex items-center justify-center min-h-[48px] min-w-[48px] px-6 py-3 bg-red-600 hover:bg-red-700 focus:ring-2 focus:ring-offset-2 focus:ring-red-500 rounded-md shadow-sm text-base font-medium text-white transition-colors duration-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Delete Invoice
                            </button>
                        </div>
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
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        @php
                                            $property = $invoice->rental->unit->property;
                                            $addressParts = [];
                                            if($property->house_building_number) $addressParts[] = $property->house_building_number;
                                            if($property->street) $addressParts[] = $property->street;
                                            if($property->village) $addressParts[] = $property->village;
                                            if($property->commune) $addressParts[] = $property->commune;
                                            if($property->district) $addressParts[] = $property->district;
                                        @endphp
                                        {{ !empty($addressParts) ? implode(', ', $addressParts) : 'Not available' }}
                                    </span>
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
        @else
            <!-- If we're not viewing a specific invoice, show the invoices list -->
            @if(!$invoice && isset($invoices) && count($invoices) > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice #</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tenant</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property & Room</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($invoices as $invoice_item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('tenant.invoice.view', $invoice_item->invoice_id) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                INV-{{ str_pad($invoice_item->invoice_id, 5, '0', STR_PAD_LEFT) }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $invoice_item->tenant_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $invoice_item->property_name ?? 'N/A' }} ({{ $invoice_item->room_number ?? 'N/A' }})
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            ${{ number_format($invoice_item->amount_due, 2) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($invoice_item->due_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @php
                                                $statusClass = match($invoice_item->payment_status) {
                                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                    'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                {{ ucfirst($invoice_item->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                @if($invoice_item->payment_status !== 'paid')
                                                    <button 
                                                        wire:click="markAsPaid({{ $invoice_item->invoice_id }})" 
                                                        wire:confirm="Are you sure you want to mark this invoice as paid?"
                                                        class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-xs"
                                                    >
                                                        Mark Paid
                                                    </button>
                                                @endif
                                                <a href="{{ route('tenant.invoice.view', $invoice_item->invoice_id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">View</a>
                                                <button wire:click="deleteInvoice({{ $invoice_item->invoice_id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    @if($perPage !== 'all')
                        {{ $invoices->links() }}
                    @else
                        <div class="text-sm text-gray-600 dark:text-gray-400 text-center">
                            Showing all {{ $invoices->count() }} results
                        </div>
                    @endif
                </div>
            @elseif(!$invoice && isset($invoices) && count($invoices) === 0)
                <div class="flex flex-col items-center justify-center bg-white dark:bg-gray-800 p-8 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="rounded-full bg-gray-100 dark:bg-gray-700 p-4 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No invoices found</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm mb-4">
                        No invoices found matching your criteria.
                    </p>
                </div>
            @endif
        @endif
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
