<div class="min-h-screen bg-stone-50 dark:bg-zinc-950 py-4">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="mb-5 flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Simple Invoice Create</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Phone-friendly flow for fast invoice entry.</p>
            </div>
            <a
                href="{{ route('invoices.index') }}"
                wire:navigate
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
            >
                Back
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-100 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-100 px-4 py-3 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-5 grid grid-cols-3 gap-2">
            @foreach ([1 => 'Choose', 2 => 'Readings', 3 => 'Check'] as $index => $label)
                <div class="rounded-lg border px-3 py-3 text-center text-sm font-medium {{ $step === $index ? 'border-blue-600 bg-blue-600 text-white' : ($step > $index ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-900/20 dark:text-emerald-300' : 'border-gray-200 bg-white text-gray-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-gray-400') }}">
                    {{ $label }}
                </div>
            @endforeach
        </div>

        <form wire:submit="save" class="space-y-5">
            @if ($step === 1)
                <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <label for="rental-search" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Find tenant, property, or room</label>
                    <input
                        id="rental-search"
                        type="text"
                        wire:model.live.debounce.250ms="search"
                        placeholder="Search name, property, room"
                        class="block w-full rounded-lg border-gray-300 px-4 py-3 text-base shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >

                    <div class="mt-4 space-y-3">
                        @forelse ($this->filteredRentals as $rental)
                            <button
                                type="button"
                                wire:click="selectRental('{{ $rental['rental_id'] }}')"
                                class="w-full rounded-xl border px-4 py-4 text-left transition {{ $selectedRental === $rental['rental_id'] ? 'border-blue-600 bg-blue-50 dark:border-blue-500 dark:bg-blue-950/40' : 'border-gray-200 bg-white hover:border-gray-300 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700' }}"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $rental['tenant_name'] }}</div>
                                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $rental['property_name'] }}</div>
                                        <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $rental['room_label'] }}</div>
                                    </div>
                                    @if ($selectedRental === $rental['rental_id'])
                                        <div class="rounded-full bg-blue-600 px-2 py-1 text-xs font-semibold text-white">Selected</div>
                                    @endif
                                </div>
                            </button>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-zinc-700 dark:text-gray-400">
                                No active tenant or room matched your search.
                            </div>
                        @endforelse
                    </div>

                    @error('selectedRental')
                        <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </section>
            @endif

            @if ($step >= 2)
                <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $selectedRentalSummary['tenant_name'] ?? 'Tenant' }}</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $selectedRentalSummary['property_name'] ?? '' }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedRentalSummary['room_label'] ?? '' }}</div>
                        </div>
                        @if ($step !== 1)
                            <button type="button" wire:click="previousStep" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-800">
                                Change
                            </button>
                        @endif
                    </div>
                </section>
            @endif

            @if ($step === 2)
                <section class="space-y-4">
                    @foreach ($readings as $utilityId => $reading)
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $reading['utility_name'] }}</h2>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Previous: {{ number_format($reading['previous_reading'], 2) }}
                                        @if ($reading['previous_date'])
                                            on {{ \Carbon\Carbon::parse($reading['previous_date'])->format('d M Y') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right text-sm">
                                    <div class="text-gray-500 dark:text-gray-400">Rate</div>
                                    <div class="font-medium text-gray-900 dark:text-white">${{ number_format($reading['rate'], 2) }}</div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">New meter reading</label>
                                <input
                                    type="number"
                                    inputmode="decimal"
                                    step="0.01"
                                    min="{{ $reading['previous_reading'] }}"
                                    wire:model.live="readings.{{ $utilityId }}.new_reading"
                                    class="block w-full rounded-lg border-gray-300 px-4 py-4 text-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                                    placeholder="Enter current number"
                                >
                                @error("readings.{$utilityId}.new_reading")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3 rounded-lg bg-stone-50 p-3 text-sm dark:bg-zinc-800/60">
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">Used</div>
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ number_format($reading['amount_used'], 2) }} {{ $reading['unit_of_measure'] }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400">Charge</div>
                                    <div class="font-semibold text-gray-900 dark:text-white">${{ number_format($reading['total_charge'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @error('readings')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </section>
            @endif

            @if ($step === 3)
                <section class="space-y-4">
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Due date</h2>
                        <div class="mt-3 grid grid-cols-3 gap-2">
                            <button type="button" wire:click="setDueDateQuick('7_days')" class="rounded-lg border border-gray-300 px-3 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-800">7 days</button>
                            <button type="button" wire:click="setDueDateQuick('15_days')" class="rounded-lg border border-gray-300 px-3 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-800">15 days</button>
                            <button type="button" wire:click="setDueDateQuick('month_end')" class="rounded-lg border border-gray-300 px-3 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-800">Month end</button>
                        </div>

                        <div class="mt-3">
                            <input
                                type="date"
                                wire:model="due_date"
                                class="block w-full rounded-lg border-gray-300 px-4 py-3 text-base shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            >
                            @error('due_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Check invoice</h2>
                        <div class="mt-4 space-y-3 text-sm">
                            @foreach ($readings as $reading)
                                @if ($reading['new_reading'] !== '' && is_numeric($reading['new_reading']))
                                    <div class="flex items-center justify-between gap-3 rounded-lg bg-stone-50 px-3 py-3 dark:bg-zinc-800/60">
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $reading['utility_name'] }}</div>
                                            <div class="text-gray-500 dark:text-gray-400">{{ number_format($reading['amount_used'], 2) }} {{ $reading['unit_of_measure'] }}</div>
                                        </div>
                                        <div class="font-semibold text-gray-900 dark:text-white">${{ number_format($reading['total_charge'], 2) }}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-4 border-t border-gray-200 pt-4 dark:border-zinc-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total amount</span>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($this->invoiceTotal, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <div class="sticky bottom-3 z-10 rounded-2xl border border-gray-200 bg-white/95 p-3 shadow-lg backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/95">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Invoice total</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">${{ number_format($this->invoiceTotal, 2) }}</div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($step > 1)
                            <button type="button" wire:click="previousStep" class="rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-800">
                                Back
                            </button>
                        @endif

                        @if ($step < 3)
                            <button type="button" wire:click="nextStep" class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                                Next
                            </button>
                        @else
                            <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                Create invoice
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
