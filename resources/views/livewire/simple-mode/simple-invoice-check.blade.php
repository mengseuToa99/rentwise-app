<div class="min-h-screen bg-stone-50 dark:bg-zinc-950 pb-32">
    <div class="mx-auto max-w-3xl px-4 pt-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.invoices_this_month') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.invoices_this_month_desc', ['month' => $monthLabel]) }}</p>
            </div>
            <a
                href="{{ route('simple-mode.home') }}"
                wire:navigate
                class="inline-flex shrink-0 items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
            >
                {{ __('app.simple_mode.back') }}
            </a>
        </div>

        {{-- Month summary --}}
        @if ($cards->isNotEmpty())
            <div class="mb-4 flex items-center justify-between rounded-2xl border border-teal-200 bg-teal-50 px-5 py-4 dark:border-teal-900/50 dark:bg-teal-950/30">
                <span class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ __('app.simple_mode.inv_summary_total') }}</span>
                <span class="text-2xl font-bold text-teal-700 dark:text-teal-300">${{ number_format($monthTotal, 2) }}</span>
            </div>
        @endif

        {{-- Invoice cards --}}
        @forelse ($cards as $card)
            <a
                href="{{ route('invoices.view', ['invoiceId' => $card['id']]) }}"
                wire:navigate
                class="group mb-3 block rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-teal-400 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-teal-700"
            >
                {{-- Top row: room + status --}}
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-1 text-sm font-bold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                            {{ __('app.simple_mode.inv_room') }} {{ $card['room'] }}
                        </span>
                    </div>
                    @php
                        $statusStyles = [
                            'paid'     => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'pending'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                            'partial'  => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                            'overdue'  => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                        ];
                        $statusClass = $statusStyles[$card['status']] ?? 'bg-gray-100 text-gray-800 dark:bg-zinc-800 dark:text-gray-300';
                        $statusKey = 'app.simple_mode.status_' . $card['status'];
                        $statusLabel = __($statusKey);
                        if ($statusLabel === $statusKey) {
                            $statusLabel = ucfirst($card['status']);
                        }
                    @endphp
                    <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Customer name --}}
                <div class="mt-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $card['customer'] }}</span>
                </div>

                {{-- Utilities billed --}}
                <div class="mt-3">
                    <div class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('app.simple_mode.inv_utilities') }}</div>
                    @if (count($card['utilities']) > 0)
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($card['utilities'] as $utility)
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    {{ $utility }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-sm text-gray-400 dark:text-gray-500">{{ __('app.simple_mode.inv_no_utilities') }}</span>
                    @endif
                </div>

                {{-- Total --}}
                <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 dark:border-zinc-800">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.inv_total') }}</span>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">${{ number_format($card['total'], 2) }}</span>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white/60 p-10 text-center dark:border-zinc-700 dark:bg-zinc-900/40">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.inv_none_this_month') }}</p>
            </div>
        @endforelse
    </div>
</div>
