<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Invoices</h1>
            <div class="flex space-x-3">
                <!-- View toggle button -->
                <button
                    wire:click="toggleDisplayMode"
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-medium text-sm text-gray-700 hover:bg-gray-300 transition-colors"
                >
                    @if($displayMode === 'card')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Table View
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Card View
                    @endif
                </button>
                
                @if($viewMode !== 'tenant')
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Add Invoice
                </a>
                @endif
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            id="search"
                            placeholder="Search invoices..." 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                    </div>
                    
                    <div>
                        <label for="statusFilter" class="block text-sm font-medium text-gray-700">Payment Status</label>
                        <select 
                            wire:model.live="statusFilter" 
                            id="statusFilter" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="rentalFilter" class="block text-sm font-medium text-gray-700">Tenant</label>
                        <select 
                            wire:model.live="rentalFilter" 
                            id="rentalFilter" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                            <option value="">All Tenants</option>
                            @foreach ($rentals as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="md:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Due Date Range</label>
                        <div class="mt-1 flex space-x-2">
                            <input 
                                type="date" 
                                wire:model.live="dateFrom"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="From"
                            >
                            <input 
                                type="date" 
                                wire:model.live="dateTo"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="To"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if ($invoices->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <p class="text-gray-500">No invoices found matching your criteria.</p>
                @if($viewMode !== 'tenant')
                <a href="{{ route('invoices.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">Create your first invoice</a>
                @endif
            </div>
        @else
            <!-- Card View -->
            @if($displayMode === 'card')
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($invoices as $invoice)
                        <div wire:key="invoice-{{ $invoice->invoice_id }}" class="perspective h-full">
                            <div class="relative w-full h-full transform transition-all duration-700 ease-in-out" 
                                x-data="{ isFlipped: false }"
                                :class="{ 'rotate-y-180': isFlipped }"
                                style="transform-style: preserve-3d; min-height: 360px;">
                                
                                <!-- Front of Card (Summary) -->
                                <div class="absolute w-full bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 backface-hidden border border-gray-200 dark:border-gray-700 dark:bg-gray-800" style="min-height: 360px;">
                                    <div class="relative">
                                        <!-- Status Badge (top right corner) -->
                                        @php
                                            $statusColors = [
                                                'paid' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'overdue' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusColor = $statusColors[$invoice->payment_status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <div class="absolute top-4 right-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                {{ ucfirst($invoice->payment_status) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Card Header -->
                                        <div class="p-5 border-b border-gray-200">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                                INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}
                                            </h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Due: {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                            </p>
                                        </div>
                                        
                                        <!-- Card Content -->
                                        <div class="p-5 space-y-3">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Tenant:</span>
                                                <span class="text-sm font-medium dark:text-white">{{ $invoice->tenant_name }}</span>
                                            </div>
                                            
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Property:</span>
                                                <span class="text-sm font-medium dark:text-white">{{ $invoice->property_name }}</span>
                                            </div>
                                            
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Room:</span>
                                                <span class="text-sm font-medium dark:text-white">{{ $invoice->room_number }}</span>
                                            </div>
                                            
                                            <div class="flex justify-between border-t border-gray-200 pt-3 mt-3">
                                                <span class="text-base font-bold text-gray-900 dark:text-white">Total:</span>
                                                <span class="text-base font-bold text-gray-900 dark:text-white">${{ number_format($invoice->amount_due, 2) }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Card Footer/Actions -->
                                        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3 flex justify-end space-x-2">
                                            @if($viewMode !== 'tenant' && $invoice->payment_status !== 'paid')
                                                <button 
                                                    wire:click="markAsPaid({{ $invoice->invoice_id }})" 
                                                    wire:confirm="Are you sure you want to mark this invoice as paid?"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 text-sm"
                                                >
                                                    Mark Paid
                                                </button>
                                            @endif
                                            
                                            @if($viewMode !== 'tenant')
                                                <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">Edit</a>
                                                <button wire:click="deleteInvoice({{ $invoice->invoice_id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">Delete</button>
                                            @endif
                                            
                                            <button 
                                                @click="isFlipped = !isFlipped" 
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                                            >
                                                Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Back of Card (Details) -->
                                <div class="absolute w-full bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 backface-hidden rotate-y-180 border border-gray-200 dark:border-gray-700 dark:bg-gray-800" style="min-height: 360px;">
                                    <div class="relative">
                                        <!-- Back Header -->
                                        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                                    Invoice Details
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                {{ ucfirst($invoice->payment_status) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Detailed Content -->
                                        <div class="p-5 space-y-4">
                                            <!-- Line Items -->
                                            <div class="border rounded-md p-3 mb-3">
                                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2 text-sm">Line Items</h4>
                                                <div class="space-y-1">
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-gray-600 dark:text-gray-400">Monthly Rent:</span>
                                                        <span class="text-gray-900 dark:text-white">${{ number_format($invoice->amount_due, 2) }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-sm font-semibold pt-1 border-t border-gray-200 dark:border-gray-700">
                                                        <span class="text-gray-900 dark:text-white">Total:</span>
                                                        <span class="text-gray-900 dark:text-white">${{ number_format($invoice->amount_due, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Payment Terms -->
                                            <div class="border rounded-md p-3">
                                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2 text-sm">Payment Terms</h4>
                                                <div class="space-y-1 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Due Date:</span>
                                                        <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Issue Date:</span>
                                                        <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->created_at)->format('M d, Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Back Card Footer/Actions -->
                                        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3 flex justify-center">
                                            <button 
                                                @click="isFlipped = !isFlipped" 
                                                class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 text-sm font-medium"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                                </svg>
                                                Back to Summary
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Table View -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property & Room</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($invoices as $invoice)
                                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('tenant.invoice.view', $invoice->invoice_id) }}'">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                            INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ $invoice->tenant_name }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ $invoice->property_name }} ({{ $invoice->room_number }})
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 font-medium">
                                            ${{ number_format($invoice->amount_due, 2) }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            @php
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                if ($invoice->payment_status === 'paid') {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                } elseif ($invoice->payment_status === 'overdue') {
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                } elseif ($invoice->payment_status === 'pending') {
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                }
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                {{ ucfirst($invoice->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium" onclick="event.stopPropagation();">
                                            <div class="flex justify-end space-x-1">
                                                @if($viewMode !== 'tenant' && $invoice->payment_status !== 'paid')
                                                    <button 
                                                        wire:click="markAsPaid({{ $invoice->invoice_id }})" 
                                                        wire:confirm="Are you sure you want to mark this invoice as paid?"
                                                        class="text-green-600 hover:text-green-900"
                                                    >
                                                        Mark Paid
                                                    </button>
                                                @endif
                                                @if($viewMode !== 'tenant')
                                                    <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    <button wire:click="deleteInvoice({{ $invoice->invoice_id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 hover:text-red-900">Delete</button>
                                                @endif
                                                <a href="{{ route('tenant.invoice.view', $invoice->invoice_id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            
            <div class="mt-6">
                {{ $invoices->links() }}
            </div>
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
            .rotate-y-180, [x-cloak][x-data] {
                transition: none !important;
            }
        }
    </style>
</div> 