<div class="min-h-screen bg-stone-50 dark:bg-zinc-950 pb-32">
    <div class="mx-auto max-w-2xl px-4 pt-4 sm:px-6">
        <div class="mb-5 flex items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('app.quick_invoice.title') }}</h1>
            <a
                href="{{ route(session('simple_mode') ? 'simple-mode.home' : 'invoices.index') }}"
                wire:navigate
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
            >
                {{ __('app.quick_invoice.back') }}
            </a>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-base text-red-700 dark:bg-red-900/20 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit="save" class="space-y-5">
            {{-- STEP 1: WHO --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-3 flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-base font-bold text-white">1</div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.quick_invoice.who_for') }}</h2>
                </div>

                @if ($selectedRental)
                    <div class="flex items-center justify-between gap-3 rounded-xl border-2 border-blue-600 bg-blue-50 p-4 dark:border-blue-500 dark:bg-blue-950/40">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-600 text-lg font-bold text-white">
                                {{ $selectedRentalSummary['initials'] ?? '?' }}
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $selectedRentalSummary['tenant_name'] ?? __('app.simple_mode.tenant') }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedRentalSummary['property_name'] ?? '' }} · {{ $selectedRentalSummary['room_label'] ?? '' }}</div>
                            </div>
                        </div>
                        <button type="button" wire:click="clearRental" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-white dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-800">
                            {{ __('app.quick_invoice.change') }}
                        </button>
                    </div>
                @else
                    @if ($this->recentRentals->isNotEmpty() && !$showAllTenants && trim($search) === '')
                        <div class="mb-3">
                            <div class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.quick_invoice.recent') }}</div>
                            <div class="space-y-2">
                                @foreach ($this->recentRentals as $rental)
                                    <button
                                        type="button"
                                        wire:click="selectRental('{{ $rental['rental_id'] }}')"
                                        class="flex w-full items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 text-left hover:border-blue-500 hover:bg-blue-50 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-500 dark:hover:bg-blue-950/30"
                                    >
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-base font-bold text-white">
                                            {{ $rental['initials'] }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ $rental['tenant_name'] }}</div>
                                            <div class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $rental['property_name'] }} · {{ $rental['room_label'] }}</div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="$set('showAllTenants', true)"
                            class="block w-full rounded-xl border-2 border-dashed border-gray-300 px-4 py-3 text-center text-base font-medium text-gray-700 hover:border-gray-400 hover:bg-gray-50 dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-800"
                        >
                            {{ __('app.quick_invoice.show_all_tenants') }}
                        </button>
                    @else
                        <div class="mb-3">
                            <input
                                type="text"
                                wire:model.live.debounce.250ms="search"
                                placeholder="{{ __('app.quick_invoice.search_placeholder') }}"
                                class="block w-full rounded-xl border-gray-300 px-4 py-3 text-base shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            >
                        </div>

                        <div class="space-y-2">
                            @forelse ($this->filteredRentals as $rental)
                                <button
                                    type="button"
                                    wire:click="selectRental('{{ $rental['rental_id'] }}')"
                                    class="flex w-full items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 text-left hover:border-blue-500 hover:bg-blue-50 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-500 dark:hover:bg-blue-950/30"
                                >
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-500 text-base font-bold text-white">
                                        {{ $rental['initials'] }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ $rental['tenant_name'] }}</div>
                                        <div class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $rental['property_name'] }} · {{ $rental['room_label'] }}</div>
                                    </div>
                                </button>
                            @empty
                                <div class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-zinc-700 dark:text-gray-400">
                                    {{ __('app.quick_invoice.no_tenants_found') }}
                                </div>
                            @endforelse
                        </div>
                    @endif

                    @error('selectedRental')
                        <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </section>

            {{-- STEP 2: READINGS --}}
            @if ($selectedRental)
                <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mb-3 flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-base font-bold text-white">2</div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.quick_invoice.meter_readings') }}</h2>
                    </div>

                    @if (empty($readings))
                        <div class="rounded-xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500 dark:border-zinc-700 dark:text-gray-400">
                            {{ __('app.quick_invoice.no_utilities') }}
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($readings as $utilityId => $reading)
                                <div class="rounded-xl border border-gray-200 bg-stone-50 p-4 dark:border-zinc-800 dark:bg-zinc-800/40">
                                    <div class="mb-3 flex items-start justify-between gap-3">
                                        <div>
                                            <div class="text-base font-bold text-gray-900 dark:text-white">{{ $reading['utility_name'] }}</div>
                                            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('app.quick_invoice.last', ['value' => number_format($reading['previous_reading'], 2)]) }}
                                                @if ($reading['previous_date'])
                                                    · {{ \Carbon\Carbon::parse($reading['previous_date'])->format('d M') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('app.quick_invoice.rate') }}</div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($reading['rate'], 2) }}</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            wire:click="adjustReading({{ $utilityId }}, -1)"
                                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border-2 border-gray-300 bg-white text-2xl font-bold text-gray-700 active:bg-gray-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-200 dark:active:bg-zinc-800"
                                            aria-label="{{ __('app.quick_invoice.decrease') }}"
                                        >−</button>

                                        <input
                                            type="number"
                                            inputmode="decimal"
                                            step="0.01"
                                            min="{{ $reading['previous_reading'] }}"
                                            wire:model.live.debounce.300ms="readings.{{ $utilityId }}.new_reading"
                                            class="block h-14 w-full rounded-xl border-2 border-gray-300 text-center text-2xl font-bold shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                                            placeholder="{{ __('app.quick_invoice.new_reading_placeholder') }}"
                                        >

                                        <button
                                            type="button"
                                            wire:click="adjustReading({{ $utilityId }}, 1)"
                                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border-2 border-gray-300 bg-white text-2xl font-bold text-gray-700 active:bg-gray-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-200 dark:active:bg-zinc-800"
                                            aria-label="{{ __('app.quick_invoice.increase') }}"
                                        >+</button>
                                    </div>

                                    <button
                                        type="button"
                                        wire:click="setSameAsLast({{ $utilityId }})"
                                        class="mt-2 w-full rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-zinc-700 dark:text-gray-200 dark:hover:bg-zinc-600"
                                    >
                                        {{ __('app.quick_invoice.same_as_last') }}
                                    </button>

                                    @error("readings.{$utilityId}.new_reading")
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    @if ($reading['new_reading'] !== '' && is_numeric($reading['new_reading']))
                                        <div class="mt-3 flex items-center justify-between rounded-lg bg-emerald-50 px-3 py-2 text-sm dark:bg-emerald-900/20">
                                            <span class="text-emerald-700 dark:text-emerald-300">
                                                {{ __('app.quick_invoice.used_amount', ['amount' => number_format($reading['amount_used'], 2), 'unit' => $reading['unit_of_measure']]) }}
                                            </span>
                                            <span class="font-bold text-emerald-700 dark:text-emerald-300">
                                                ${{ number_format($reading['total_charge'], 2) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @error('readings')
                            <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @endif
                </section>

            @endif
        </form>
    </div>

    {{-- Sticky bottom bar --}}
    @if ($selectedRental)
        <div class="fixed inset-x-0 bottom-0 z-20 border-t border-gray-200 bg-white/95 px-4 py-3 shadow-[0_-4px_12px_rgba(0,0,0,0.08)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/95">
            <div class="mx-auto flex max-w-2xl items-center justify-between gap-3">
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('app.quick_invoice.total') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($this->invoiceTotal, 2) }}</div>
                </div>
                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    @disabled(!$this->hasAtLeastOneReading())
                    class="rounded-xl bg-emerald-600 px-6 py-4 text-base font-bold text-white shadow-sm hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-gray-300 dark:disabled:bg-zinc-700"
                >
                    <span wire:loading.remove wire:target="save">{{ __('app.quick_invoice.create_invoice') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.quick_invoice.saving') }}</span>
                </button>
            </div>
        </div>
    @endif
</div>
