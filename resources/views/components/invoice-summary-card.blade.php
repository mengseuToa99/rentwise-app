@props(['totalCount', 'paidCount', 'pendingCount'])

<div class="bg-white dark:bg-zinc-900 p-3 rounded-lg shadow-md border border-gray-200 dark:border-zinc-800">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Total Invoices</h3>
        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCount }}</span>
    </div>
    
    <!-- Progress bar -->
    <div class="h-2 bg-gray-200 dark:bg-zinc-700 rounded-full w-full mb-3">
        @php
            $paidPercentage = $totalCount > 0 ? ($paidCount / $totalCount) * 100 : 0;
        @endphp
        <div class="h-full bg-green-500 rounded-full" style="width: {{ $paidPercentage }}%"></div>
    </div>
    
    <!-- Status counts -->
    <div class="grid grid-cols-2 gap-2">
        <div class="bg-gray-100 dark:bg-zinc-800 p-2 rounded-md">
            <div class="text-center text-green-600 dark:text-green-400 font-medium">Paid</div>
            <div class="text-center text-2xl font-bold text-green-600 dark:text-green-500">{{ $paidCount }}</div>
        </div>
        <div class="bg-gray-100 dark:bg-zinc-800 p-2 rounded-md">
            <div class="text-center text-amber-600 dark:text-amber-400 font-medium">Pending</div>
            <div class="text-center text-2xl font-bold text-amber-600 dark:text-amber-500">{{ $pendingCount }}</div>
        </div>
    </div>
    
    <!-- Action button -->
    <a href="{{ route('landlord.invoices') }}" class="mt-3 block w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white py-2 px-4 rounded-md text-center font-medium transition-colors">
        Manage Invoices
    </a>
</div> 