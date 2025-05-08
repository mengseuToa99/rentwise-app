<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Invoices</h1>
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Add Invoice
            </a>
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
                <a href="{{ route('invoices.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">Create your first invoice</a>
            </div>
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property & Room</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->tenant_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->property_name }} (Room {{ $invoice->room_number }})
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        ${{ number_format($invoice->amount_due, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ ucfirst($invoice->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            @if ($invoice->payment_status !== 'paid')
                                                <button 
                                                    wire:click="markAsPaid({{ $invoice->invoice_id }})" 
                                                    wire:confirm="Are you sure you want to mark this invoice as paid?"
                                                    class="text-green-600 hover:text-green-900"
                                                >
                                                    Mark Paid
                                                </button>
                                            @endif
                                            <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <button wire:click="deleteInvoice({{ $invoice->invoice_id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 hover:text-red-900">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-6">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div> 