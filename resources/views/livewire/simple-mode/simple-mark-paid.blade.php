<div class="min-h-screen bg-stone-50 dark:bg-zinc-950 pb-32">
    <div class="mx-auto max-w-3xl px-4 pt-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.pay_title') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.pay_subtitle') }}</p>
            </div>
            <a
                href="{{ route('simple-mode.home') }}"
                wire:navigate
                class="inline-flex shrink-0 items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
            >
                {{ __('app.simple_mode.back') }}
            </a>
        </div>

        {{-- Flash confirmation --}}
        @if ($flash !== '')
            <div
                wire:key="flash"
                class="mb-4 flex items-center gap-2 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-300"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm font-medium">{{ $flash }}</span>
            </div>
        @endif

        {{-- Outstanding summary --}}
        @if ($cards->isNotEmpty())
            <div class="mb-4 flex items-center justify-between rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 dark:border-amber-900/50 dark:bg-amber-950/30">
                <span class="text-sm font-medium text-amber-800 dark:text-amber-200">{{ __('app.simple_mode.pay_total_outstanding') }}</span>
                <span class="text-2xl font-bold text-amber-700 dark:text-amber-300">${{ number_format($outstandingTotal, 2) }}</span>
            </div>
        @endif

        {{-- Unpaid invoice cards --}}
        @forelse ($cards as $card)
            <div
                wire:key="inv-{{ $card['id'] }}"
                class="mb-3 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
            >
                {{-- Top row: room + status --}}
                <div class="flex items-center justify-between gap-3">
                    <span class="inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-1 text-sm font-bold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                        {{ __('app.simple_mode.inv_room') }} {{ $card['room'] }}
                    </span>
                    @php
                        $statusStyles = [
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

                {{-- Amounts: total / paid / remaining --}}
                <div class="mt-4 grid grid-cols-3 gap-2 rounded-xl bg-stone-50 p-3 text-center dark:bg-zinc-800/50">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('app.simple_mode.inv_total') }}</div>
                        <div class="mt-0.5 text-base font-bold text-gray-900 dark:text-white">${{ number_format($card['total'], 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('app.simple_mode.pay_paid') }}</div>
                        <div class="mt-0.5 text-base font-bold text-green-600 dark:text-green-400">${{ number_format($card['paid'], 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ __('app.simple_mode.pay_remaining') }}</div>
                        <div class="mt-0.5 text-base font-bold text-amber-600 dark:text-amber-400">${{ number_format($card['outstanding'], 2) }}</div>
                    </div>
                </div>

                {{-- Quick action: paid in full --}}
                <button
                    type="button"
                    wire:click="payFull({{ $card['id'] }})"
                    wire:loading.attr="disabled"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-green-700 disabled:opacity-50"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('app.simple_mode.pay_full') }}
                </button>

                {{-- Enter a payment amount (full or partial) --}}
                <div class="mt-2 flex items-stretch gap-2">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-base font-medium text-gray-400 dark:text-gray-500">$</span>
                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            inputmode="decimal"
                            wire:model="amount.{{ $card['id'] }}"
                            placeholder="{{ __('app.simple_mode.pay_other_placeholder') }}"
                            class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-7 pr-3 text-base text-gray-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                        />
                    </div>
                    <button
                        type="button"
                        wire:click="payCustom({{ $card['id'] }})"
                        wire:loading.attr="disabled"
                        class="inline-flex shrink-0 items-center justify-center rounded-xl border border-teal-600 bg-teal-50 px-4 py-3 text-base font-bold text-teal-700 transition hover:bg-teal-100 disabled:opacity-50 dark:border-teal-700 dark:bg-teal-950/30 dark:text-teal-300 dark:hover:bg-teal-950/50"
                    >
                        {{ __('app.simple_mode.pay_record') }}
                    </button>
                </div>

                {{-- Cancel --}}
                <button
                    type="button"
                    wire:click="cancelInvoice({{ $card['id'] }})"
                    wire:confirm="{{ __('app.simple_mode.pay_cancel_confirm') }}"
                    wire:loading.attr="disabled"
                    class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 disabled:opacity-50 dark:border-red-900/50 dark:text-red-400 dark:hover:bg-red-950/30"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ __('app.simple_mode.pay_cancel') }}
                </button>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white/60 p-10 text-center dark:border-zinc-700 dark:bg-zinc-900/40">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-green-300 dark:text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.pay_all_clear') }}</p>
            </div>
        @endforelse
    </div>
</div>
