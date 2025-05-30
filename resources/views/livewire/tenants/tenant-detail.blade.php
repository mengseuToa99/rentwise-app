<div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Header with back button -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('tenants.index') }}" class="mr-4 flex items-center text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" wire:navigate>
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Tenants
                </a>
                <div class="flex gap-2">
                    <a href="{{ route('rentals.create', ['tenant' => $tenant->user_id]) }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-700 dark:hover:bg-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Rental
                    </a>
                    <a href="{{ route('invoices.create', ['tenant' => $tenant->user_id]) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-zinc-800 dark:text-gray-300 dark:ring-zinc-700 dark:hover:bg-zinc-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Create Invoice
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Paid -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Paid</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($statistics['total_paid'], 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Due -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Amount Due</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($statistics['total_due'], 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- On-Time Payments -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">On-Time Payments</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $statistics['on_time_percentage'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lease Status -->
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-900">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Lease Status</dt>
                                <dd class="mt-1 flex items-center">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $statistics['lease_status'] }}</span>
                                    @if($statistics['remaining_days'] > 0)
                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ $statistics['remaining_days'] }} days left)</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
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
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Rental History</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Current and past rental agreements.</p>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Total: {{ $statistics['total_rentals'] }} | Active: {{ $statistics['active_rentals'] }}
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="min-w-full">
                    <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="w-3/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Property & Unit</th>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</th>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rent</th>
                                <th scope="col" class="w-3/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Period</th>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-zinc-900">
                            @forelse($rentals as $rental)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $rental->property_name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Unit {{ $rental->room_number }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 truncate">{{ $rental->room_type }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">${{ number_format($rental->rent_amount, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ date('M d, Y', strtotime($rental->start_date)) }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">to {{ date('M d, Y', strtotime($rental->end_date)) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
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
                                    <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">No rental history found.</td>
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
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Invoice History</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">All invoices issued to this tenant.</p>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Total: {{ $statistics['total_invoices'] }} | Pending: {{ $statistics['pending_invoices'] }}
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="min-w-full">
                    <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Invoice #</th>
                                <th scope="col" class="w-3/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Property & Unit</th>
                                <th scope="col" class="w-3/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Dates</th>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-zinc-900">
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">INV-{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $invoice->property_name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Unit {{ $invoice->room_number }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Issued: {{ date('M d, Y', strtotime($invoice->created_at)) }}</div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Due: {{ date('M d, Y', strtotime($invoice->due_date)) }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">${{ number_format($invoice->amount_due, 2) }}</td>
                                    <td class="px-4 py-3">
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
                                    <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">No invoice history found.</td>
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
                <div class="min-w-full">
                    <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Payment ID</th>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Invoice #</th>
                                <th scope="col" class="w-3/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Property & Unit</th>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                                <th scope="col" class="w-2/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                                <th scope="col" class="w-1/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Method</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-zinc-900">
                            @forelse($paymentHistory as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white truncate">{{ $payment->payment_id }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">INV-{{ str_pad($payment->invoice_id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $payment->property_name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Unit {{ $payment->room_number }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ date('M d, Y', strtotime($payment->payment_date)) }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-green-600 dark:text-green-400">${{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white truncate">{{ ucfirst($payment->payment_method) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">No payment history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 