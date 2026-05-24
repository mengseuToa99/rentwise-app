<div class="min-h-screen bg-stone-50 dark:bg-zinc-950 pb-32">
    <div class="mx-auto max-w-3xl px-4 pt-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-5 flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.move_out_title') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.move_out_subtitle') }}</p>
            </div>
            <a
                href="{{ route('simple-mode.home') }}"
                wire:navigate
                class="inline-flex shrink-0 items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
            >
                {{ __('app.simple_mode.back') }}
            </a>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        {{-- Step indicator --}}
        <div class="mb-5 grid grid-cols-2 gap-2">
            @foreach ([1 => __('app.simple_mode.mo_choose'), 2 => __('app.simple_mode.mo_confirm')] as $index => $label)
                <div class="rounded-lg border px-3 py-3 text-center text-sm font-medium {{ $step === $index ? 'border-rose-600 bg-rose-600 text-white' : ($step > $index ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-900/20 dark:text-emerald-300' : 'border-gray-200 bg-white text-gray-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-gray-400') }}">
                    {{ $label }}
                </div>
            @endforeach
        </div>

        {{-- ============================ STEP 1: pick occupied room ============================ --}}
        @if ($step === 1)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <label for="room-search" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.mo_pick_room') }}</label>
                <input
                    id="room-search"
                    type="text"
                    wire:model.live.debounce.250ms="search"
                    placeholder="{{ __('app.simple_mode.mo_search_placeholder') }}"
                    class="mb-3 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-base text-gray-900 shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
                >

                <div class="space-y-2">
                    @forelse ($this->filteredRentals as $rental)
                        <button
                            type="button"
                            wire:click="selectRental('{{ $rental['rental_id'] }}')"
                            class="flex w-full items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-left transition hover:border-rose-400 hover:bg-rose-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-rose-700 dark:hover:bg-rose-950/20"
                        >
                            <div class="min-w-0">
                                <div class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ $rental['tenant_name'] }}</div>
                                <div class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $rental['property_name'] }} · {{ $rental['room_label'] }}</div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @empty
                        <div class="rounded-xl bg-gray-50 px-4 py-8 text-center text-sm text-gray-500 dark:bg-zinc-800 dark:text-gray-400">
                            {{ __('app.simple_mode.mo_no_occupied') }}
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- ============================ STEP 2: final readings + confirm ============================ --}}
        @if ($step === 2)
            <form wire:submit="moveOut" class="space-y-4">
                {{-- Who/what is leaving --}}
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/50 dark:bg-rose-950/20">
                    <div class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">{{ __('app.simple_mode.mo_leaving') }}</div>
                    <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $selectedRentalSummary['tenant_name'] ?? '' }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $selectedRentalSummary['property_name'] ?? '' }} · {{ $selectedRentalSummary['room_label'] ?? '' }}</div>
                </div>

                {{-- Move-out date --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <label for="move-out-date" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.mo_move_out_date') }}</label>
                    <input
                        id="move-out-date"
                        type="date"
                        wire:model="move_out_date"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-base text-gray-900 shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
                    >
                    @error('move_out_date') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Final meter readings --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">{{ __('app.simple_mode.mo_final_readings') }}</div>
                    <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.mo_final_readings_hint') }}</p>

                    @if (count($readings) > 0)
                        <div class="space-y-3">
                            @foreach ($readings as $utilityId => $reading)
                                <div class="rounded-xl border border-gray-200 p-3 dark:border-zinc-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $reading['utility_name'] }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.mo_last', ['value' => rtrim(rtrim(number_format($reading['previous_reading'], 2), '0'), '.')]) }}</span>
                                    </div>
                                    <div class="mt-2 flex items-center gap-2">
                                        <input
                                            type="number"
                                            step="any"
                                            min="0"
                                            inputmode="decimal"
                                            wire:model.live.debounce.300ms="readings.{{ $utilityId }}.new_reading"
                                            placeholder="{{ __('app.simple_mode.mo_new_reading') }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-base text-gray-900 shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
                                        >
                                        <span class="shrink-0 text-sm text-gray-500 dark:text-gray-400">{{ $reading['unit_of_measure'] }}</span>
                                    </div>
                                    @if (($reading['amount_used'] ?? 0) > 0)
                                        <div class="mt-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ __('app.simple_mode.mo_used', ['amount' => rtrim(rtrim(number_format($reading['amount_used'], 2), '0'), '.'), 'unit' => $reading['unit_of_measure']]) }}</div>
                                    @endif
                                    @error("readings.{$utilityId}.new_reading") <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('app.simple_mode.inv_no_utilities') }}</p>
                    @endif
                </div>

                {{-- Final invoice total --}}
                @if ($this->invoiceTotal > 0)
                    <div class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 dark:border-emerald-900/50 dark:bg-emerald-950/30">
                        <span class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ __('app.simple_mode.mo_invoice_total') }}</span>
                        <span class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">${{ number_format($this->invoiceTotal, 2) }}</span>
                    </div>
                @endif

                {{-- Warning + actions --}}
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-300">
                    {{ __('app.simple_mode.mo_warning') }}
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="backToList"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
                    >
                        {{ __('app.simple_mode.prev') }}
                    </button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="moveOut"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2.5 text-base font-semibold text-white shadow-sm hover:bg-rose-700 disabled:opacity-60"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span wire:loading.remove wire:target="moveOut">{{ __('app.simple_mode.mo_confirm_button') }}</span>
                        <span wire:loading wire:target="moveOut">{{ __('app.simple_mode.saving') }}</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
