<div class="py-4 bg-gray-50 dark:bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Invoices</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage all your property invoices and payments</p>
            </div>
            <div class="flex space-x-3">
                <!-- View toggle button -->
                <button
                    wire:click="toggleDisplayMode"
                    type="button"
                    class="inline-flex items-center px-3 py-2 bg-gray-200 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-zinc-700 transition-colors"
                >
                    @if($displayMode === 'card')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Table View
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Card View
                    @endif
                </button>
                
                @if($viewMode !== 'tenant')
                <div class="flex space-x-2">
                    <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Invoice
                    </a>
                    <a href="{{ route('invoices.bulk-create') }}" class="inline-flex items-center px-3 py-2 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Bulk Create
                    </a>
                </div>
                @endif
            </div>
        </div>
        
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
        
        <div class="bg-white dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm mb-6">
            <div class="p-4">
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
                                class="block w-full pl-10 py-2 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                        </div>
                    </div>
                    
                    <div>
                        <label for="statusFilter" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Payment Status</label>
                        <select 
                            wire:model.live="statusFilter" 
                            id="statusFilter" 
                            class="block w-full py-2 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="propertyFilter" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Property</label>
                        <select 
                            wire:model.live="propertyFilter" 
                            id="propertyFilter" 
                            class="block w-full py-2 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">All Properties</option>
                            @foreach ($properties as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="rentalFilter" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tenant</label>
                        <select 
                            wire:model.live="rentalFilter" 
                            id="rentalFilter" 
                            class="block w-full py-2 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
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
                                class="block w-full py-2 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="From"
                            >
                            <input 
                                type="date" 
                                wire:model.live="dateTo"
                                class="block w-full py-2 rounded-md bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm shadow-sm dark:text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="To"
                            >
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end mt-3 border-t dark:border-zinc-700 pt-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Rows per page:</span>
                        <select wire:model.live="perPage" class="bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md text-sm py-1 px-3 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($paginationOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        @if ($invoices->isEmpty())
            <div class="flex flex-col items-center justify-center bg-white dark:bg-zinc-900 p-8 rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm">
                <div class="rounded-full bg-gray-100 dark:bg-zinc-800 p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No invoices found</h3>
                <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm mb-4">
                    @if($viewMode === 'tenant')
                        You don't have any invoices yet.
                    @else
                        No invoices found matching your criteria.
                    @endif
                </p>
                @if($viewMode !== 'tenant')
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-medium text-sm text-white shadow-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create your first invoice
                </a>
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
                                style="transform-style: preserve-3d; min-height: 500px;">
                                
                                <!-- Front of Card (Summary) -->
                                <div class="absolute w-full bg-white dark:bg-zinc-900 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300 backface-hidden border border-gray-200 dark:border-zinc-800" style="min-height: 500px;">
                                    <div class="relative h-full flex flex-col">
                                        <!-- Card Header with Status -->
                                        <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                                        INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}
                                                    </h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        Due: {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                                    </p>
                                                </div>
                                                @php
                                                    $statusColors = [
                                                        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                        'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                    ];
                                                    $statusColor = $statusColors[$invoice->payment_status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                                    {{ ucfirst($invoice->payment_status) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Card Content -->
                                        <div class="p-4 space-y-4 flex-grow">
                                            <!-- Basic Info -->
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tenant</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->tenant_name }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Property</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->property_name }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Room</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->room_number }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Issue Date</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->created_at)->format('M d, Y') }}</p>
                                                </div>
                                            </div>

                                            <!-- Utility Readings -->
                                            @if(isset($invoice->utility_readings) && count($invoice->utility_readings) > 0)
                                                <div class="border-t border-gray-200 dark:border-zinc-700 pt-3">
                                                    <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Utility Readings</h4>
                                                    <div class="space-y-2">
                                                        @foreach($invoice->utility_readings as $reading)
                                                            <div class="bg-gray-50 dark:bg-zinc-800 rounded-md p-2">
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $reading->utility_name }}</span>
                                                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                                                        {{ $reading->previous_reading }} → {{ $reading->new_reading }}
                                                                    </span>
                                                                </div>
                                                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                                                    <span>Usage: {{ $reading->usage_amount }} units</span>
                                                                    <span>Rate: ${{ number_format($reading->rate, 2) }}/unit</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Total Amount -->
                                            <div class="border-t border-gray-200 dark:border-zinc-700 pt-3 mt-auto">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Total Amount</span>
                                                    <span class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($invoice->amount_due, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Card Footer/Actions -->
                                        <div class="bg-gray-50 dark:bg-zinc-800 px-4 py-3 flex justify-end space-x-2 border-t border-gray-200 dark:border-zinc-700">
                                            @if($viewMode !== 'tenant' && $invoice->payment_status !== 'paid')
                                                <button 
                                                    wire:click="markAsPaid({{ $invoice->invoice_id }})" 
                                                    wire:confirm="Are you sure you want to mark this invoice as paid?"
                                                    class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-xs"
                                                >
                                                    Mark Paid
                                                </button>
                                            @endif
                                            
                                            @if($viewMode !== 'tenant')
                                                <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">Edit</a>
                                                <button wire:click="deleteInvoice({{ $invoice->invoice_id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs">Delete</button>
                                            @endif
                                            
                                            <button 
                                                @click="isFlipped = !isFlipped" 
                                                class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs"
                                            >
                                                <span>Details</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Back of Card (Details) -->
                                <div class="absolute w-full bg-white dark:bg-zinc-900 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300 backface-hidden rotate-y-180 border border-gray-200 dark:border-zinc-800" style="min-height: 500px;">
                                    <div class="relative h-full flex flex-col">
                                        <!-- Back Header -->
                                        <div class="p-5 border-b border-gray-200 dark:border-zinc-700 flex justify-between items-center">
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                                    Invoice Details
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                {{ ucfirst($invoice->payment_status) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Detailed Content -->
                                        <div class="p-5 space-y-4 flex-grow">
                                            <!-- Basic Info -->
                                            <div class="border border-gray-200 dark:border-zinc-700 rounded-md p-3">
                                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2 text-sm">Basic Information</h4>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Tenant:</span>
                                                        <span class="text-gray-900 dark:text-white">{{ $invoice->tenant_name }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Property:</span>
                                                        <span class="text-gray-900 dark:text-white">{{ $invoice->property_name }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Room:</span>
                                                        <span class="text-gray-900 dark:text-white">{{ $invoice->room_number }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Utility Readings -->
                                            @if(isset($invoice->utility_readings) && count($invoice->utility_readings) > 0)
                                                <div class="border border-gray-200 dark:border-zinc-700 rounded-md p-3">
                                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2 text-sm">Utility Readings</h4>
                                                    <div class="space-y-3">
                                                        @foreach($invoice->utility_readings as $reading)
                                                            <div class="border-b border-gray-200 dark:border-zinc-700 last:border-0 pb-2 last:pb-0">
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $reading->utility_name }}</span>
                                                                </div>
                                                                <div class="grid grid-cols-2 gap-2 text-sm">
                                                                    <div>
                                                                        <span class="text-gray-600 dark:text-gray-400">Previous:</span>
                                                                        <span class="text-gray-900 dark:text-white ml-1">{{ $reading->previous_reading }}</span>
                                                                    </div>
                                                                    <div>
                                                                        <span class="text-gray-600 dark:text-gray-400">New:</span>
                                                                        <span class="text-gray-900 dark:text-white ml-1">{{ $reading->new_reading }}</span>
                                                                    </div>
                                                                    <div>
                                                                        <span class="text-gray-600 dark:text-gray-400">Usage:</span>
                                                                        <span class="text-gray-900 dark:text-white ml-1">{{ $reading->usage_amount }} units</span>
                                                                    </div>
                                                                    <div>
                                                                        <span class="text-gray-600 dark:text-gray-400">Rate:</span>
                                                                        <span class="text-gray-900 dark:text-white ml-1">${{ number_format($reading->rate, 2) }}/unit</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Payment Details -->
                                            <div class="border border-gray-200 dark:border-zinc-700 rounded-md p-3">
                                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2 text-sm">Payment Details</h4>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Due Date:</span>
                                                        <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Issue Date:</span>
                                                        <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->created_at)->format('M d, Y') }}</span>
                                                    </div>
                                                    <div class="flex justify-between font-semibold pt-2 border-t border-gray-200 dark:border-zinc-700">
                                                        <span class="text-gray-900 dark:text-white">Total Amount:</span>
                                                        <span class="text-gray-900 dark:text-white">${{ number_format($invoice->amount_due, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Back Card Footer/Actions -->
                                        <div class="bg-gray-50 dark:bg-zinc-800 px-5 py-3 flex justify-center">
                                            <button 
                                                @click="isFlipped = !isFlipped" 
                                                class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white text-xs font-medium"
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
                <div class="bg-white dark:bg-zinc-900 overflow-hidden rounded-lg border border-gray-200 dark:border-zinc-800 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead class="bg-gray-50 dark:bg-zinc-800">
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
                            <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                @foreach ($invoices as $invoice)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors cursor-pointer" onclick="window.location='{{ route('tenant.invoice.view', $invoice->invoice_id) }}'">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $invoice->tenant_name }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $invoice->property_name }} ({{ $invoice->room_number }})
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            ${{ number_format($invoice->amount_due, 2) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @php
                                                $statusClass = match($invoice->payment_status) {
                                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                    'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                {{ ucfirst($invoice->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium" onclick="event.stopPropagation();">
                                            <div class="flex justify-end space-x-2">
                                                @if($viewMode !== 'tenant' && $invoice->payment_status !== 'paid')
                                                    <button 
                                                        wire:click="markAsPaid({{ $invoice->invoice_id }})" 
                                                        wire:confirm="Are you sure you want to mark this invoice as paid?"
                                                        class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-xs"
                                                    >
                                                        Mark Paid
                                                    </button>
                                                @endif
                                                @if($viewMode !== 'tenant')
                                                    <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">Edit</a>
                                                    <button wire:click="deleteInvoice({{ $invoice->invoice_id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs">Delete</button>
                                                @endif
                                                <a href="{{ route('tenant.invoice.view', $invoice->invoice_id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs">View</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            
            <div class="mt-4">
                @if($perPage !== 'all')
                    {{ $invoices->links() }}
                @else
                    <div class="text-sm text-gray-600 dark:text-gray-400 text-center">
                        Showing all {{ $invoices->count() }} results
                    </div>
                @endif
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

        [x-data] {
            transition-property: transform;
            transition-duration: 0.7s;
            transition-timing-function: ease-in-out;
            transform-style: preserve-3d;
        }
        
        /* For users who prefer reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .rotate-y-180, [x-cloak][x-data] {
                transition: none !important;
            }
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            // Ensure Alpine.js is properly initializing the card flip animation
            if (window.Alpine) {
                window.Alpine.initTree(document.body);
            }
        });

        document.addEventListener('livewire:navigated', () => {
            // Re-initialize Alpine after navigation
            if (window.Alpine) {
                window.Alpine.initTree(document.body);
            }
        });
    </script>
    @endpush
</div> 