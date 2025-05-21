<div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Header with back button -->
        <div class="mb-6">
            <div class="flex items-center">
                <a href="{{ route('tenants.index') }}" class="mr-4 flex items-center text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" wire:navigate>
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Tenants
                </a>
            </div>
        </div>

        <!-- Tenant Profile Section -->
        <div class="mb-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-zinc-900">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Tenant Information</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Personal details and contact information.</p>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 dark:bg-zinc-800 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:col-span-2 sm:mt-0">{{ $tenant->first_name }} {{ $tenant->last_name }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 dark:bg-zinc-900 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email address</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:col-span-2 sm:mt-0">{{ $tenant->email }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 dark:bg-zinc-800 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone number</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:col-span-2 sm:mt-0">{{ $tenant->phone_number }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 dark:bg-zinc-900 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Username</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:col-span-2 sm:mt-0">{{ $tenant->username }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 dark:bg-zinc-800 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                            @if($tenant->status === 'active')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">Active</span>
                            @elseif($tenant->status === 'inactive')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">Inactive</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($tenant->status) }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Rental History Section -->
        <div class="mb-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-zinc-900">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Rental History</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Current and past rental agreements.</p>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Property</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Unit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Start Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">End Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-zinc-900">
                            @forelse($rentals as $rental)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $rental->property_name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $rental->room_number }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ date('M d, Y', strtotime($rental->start_date)) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ date('M d, Y', strtotime($rental->end_date)) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($rental->status === 'active')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">Active</span>
                                        @elseif($rental->status === 'expired')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">Expired</span>
                                        @elseif($rental->status === 'terminated')
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">Terminated</span>
                                        @elseif($rental->status === 'pending')
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No rental history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Invoice History Section -->
        <div class="mb-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-zinc-900">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Invoice History</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">All invoices issued to this tenant.</p>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Invoice #</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Property</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Unit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Issue Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Due Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-zinc-900">
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $invoice->property_name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $invoice->room_number }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ date('M d, Y', strtotime($invoice->created_at)) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ date('M d, Y', strtotime($invoice->due_date)) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">${{ number_format($invoice->amount_due, 2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($invoice->payment_status === 'paid')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">Paid</span>
                                        @elseif($invoice->payment_status === 'pending')
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Pending</span>
                                        @elseif($invoice->payment_status === 'overdue')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">Overdue</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($invoice->payment_status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No invoice history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment History Section -->
        <div class="mb-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-zinc-900">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Payment History</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">All payments made by this tenant.</p>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Payment ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Invoice #</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Property</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Payment Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Method</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-zinc-900">
                            @forelse($paymentHistory as $payment)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $payment->payment_id }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">INV-{{ str_pad($payment->invoice_id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $payment->property_name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ date('M d, Y', strtotime($payment->payment_date)) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600 dark:text-green-400">${{ number_format($payment->amount, 2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">{{ ucfirst($payment->payment_method) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No payment history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 