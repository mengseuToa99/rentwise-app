<div class="min-h-screen bg-stone-50 dark:bg-zinc-950 pb-32">
    <div class="mx-auto max-w-2xl px-4 pt-4 sm:px-6">

        <div class="mb-5 flex items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.add_tenant') }}</h1>
            <a
                href="{{ route('simple-mode.home') }}"
                wire:navigate
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
            >
                {{ __('app.simple_mode.back') }}
            </a>
        </div>

        {{-- Stage chips --}}
        <div class="mb-5 grid grid-cols-3 gap-2">
            @foreach ([1 => __('app.simple_mode.step_person'), 2 => __('app.simple_mode.step_room'), 3 => __('app.simple_mode.step_confirm')] as $index => $label)
                <div class="rounded-lg border px-3 py-2 text-center text-xs font-semibold {{ $step === $index ? 'border-orange-600 bg-orange-600 text-white' : ($step > $index ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-900/20 dark:text-emerald-300' : 'border-gray-200 bg-white text-gray-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-gray-400') }}">
                    {{ $index }}. {{ $label }}
                </div>
            @endforeach
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-base text-red-700 dark:bg-red-900/20 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        {{-- STEP 1: PERSON --}}
        @if ($step === 1)
            <section class="space-y-4">
                @if ($selectedTenant)
                    {{-- Picked tenant --}}
                    <div class="rounded-2xl border-2 border-orange-600 bg-orange-50 p-4 shadow-sm dark:border-orange-500 dark:bg-orange-950/40">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-xs font-bold uppercase tracking-wide text-orange-700 dark:text-orange-300">{{ __('app.simple_mode.tenant') }}</div>
                                <div class="mt-1 truncate text-lg font-bold text-gray-900 dark:text-white">{{ $selectedTenant['first_name'] }} {{ $selectedTenant['last_name'] }}</div>
                                <div class="truncate text-sm text-gray-600 dark:text-gray-300">
                                    {{ $selectedTenant['phone_number'] }}@if ($selectedTenant['email'] && !str_ends_with($selectedTenant['email'], '@tenant.local')) · {{ $selectedTenant['email'] }}@endif
                                </div>
                            </div>
                            <button type="button" wire:click="clearExistingTenant" class="shrink-0 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800">
                                {{ __('app.simple_mode.change') }}
                            </button>
                        </div>
                    </div>
                @else
                    {{-- Search existing tenants --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.find_tenant') }}</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="tenantSearch"
                            placeholder="{{ __('app.simple_mode.find_tenant_placeholder') }}"
                            class="block w-full rounded-xl border-gray-300 px-4 py-3 text-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            autofocus
                        >
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.find_tenant_hint') }}</p>
                    </div>

                    @if (!empty($tenantResults))
                        <div class="space-y-2">
                            @foreach ($tenantResults as $t)
                                <button
                                    type="button"
                                    wire:click="selectExistingTenant({{ $t['user_id'] }})"
                                    class="flex w-full items-center gap-3 rounded-2xl border-2 border-gray-200 bg-white p-4 text-left transition hover:border-orange-400 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-orange-500"
                                >
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 text-base font-bold text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">
                                        {{ strtoupper(substr($t['first_name'] ?: '?', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-base font-bold text-gray-900 dark:text-white">{{ $t['first_name'] }} {{ $t['last_name'] }}</div>
                                        <div class="truncate text-sm text-gray-500 dark:text-gray-400">
                                            {{ $t['phone_number'] }}@if ($t['email'] && !str_ends_with($t['email'], '@tenant.local')) · {{ $t['email'] }}@endif
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @elseif (strlen(trim($tenantSearch)) >= 2)
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-400">
                            {{ __('app.simple_mode.no_tenant_match', ['term' => $tenantSearch]) }}
                        </div>
                    @endif

                    @error('existingTenantId') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                @endif
            </section>
        @endif

        {{-- STEP 2: ROOM --}}
        @if ($step === 2)
            <section class="space-y-3">
                @forelse ($availableRooms as $room)
                    @php $isSelected = $roomId === $room['room_id']; @endphp
                    <button
                        type="button"
                        wire:click="selectRoom('{{ $room['room_id'] }}')"
                        class="flex w-full items-center gap-3 rounded-2xl border-2 p-4 text-left transition {{ $isSelected ? 'border-orange-600 bg-orange-50 dark:border-orange-500 dark:bg-orange-950/40' : 'border-gray-200 bg-white hover:border-gray-300 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700' }}"
                    >
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md border-2 {{ $isSelected ? 'border-orange-600 bg-orange-600' : 'border-gray-300 dark:border-zinc-600' }}">
                            @if ($isSelected)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-base font-bold text-gray-900 dark:text-white">{{ $room['label'] }}</div>
                            <div class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $room['property_name'] }}</div>
                        </div>
                        <div class="shrink-0 text-right">
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.rent') }}</div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format($room['rent_amount'], 0) }}</div>
                        </div>
                    </button>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-400">
                        {{ __('app.simple_mode.no_empty_rooms') }}
                    </div>
                @endforelse
                @error('roomId') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </section>
        @endif

        {{-- STEP 3: CONFIRM --}}
        @if ($step === 3)
            <section class="space-y-4">
                @if ($selectedTenant)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="mb-3 text-base font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.tenant') }}</h2>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $selectedTenant['first_name'] }} {{ $selectedTenant['last_name'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $selectedTenant['phone_number'] }}@if ($selectedTenant['email'] && !str_ends_with($selectedTenant['email'], '@tenant.local')) · {{ $selectedTenant['email'] }}@endif
                        </div>
                    </div>
                @endif

                @if ($this->selectedRoom)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="mb-3 text-base font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.room') }}</h2>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->selectedRoom['label'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $this->selectedRoom['property_name'] }}</div>
                    </div>
                @endif

                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.start_date') }}</label>
                    <input type="date" wire:model="start_date" class="block w-full rounded-xl border-gray-300 px-4 py-3 text-base shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @error('start_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.monthly_rent') }}</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-bold text-gray-500 dark:text-gray-400">$</span>
                        <input type="number" inputmode="decimal" step="0.01" min="0" wire:model="monthly_rent" placeholder="0.00" class="block w-full rounded-xl border-gray-300 pl-9 pr-4 py-3 text-lg font-bold shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    </div>
                    @error('monthly_rent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </section>
        @endif
    </div>

    {{-- Floating liquid-glass action bar --}}
    <div class="fixed bottom-3 left-1/2 z-30 w-[calc(100%-1.5rem)] max-w-2xl -translate-x-1/2 rounded-2xl border border-white/40 bg-white/70 px-3 py-2.5 shadow-[0_8px_32px_rgba(0,0,0,0.12)] backdrop-blur-2xl backdrop-saturate-150 sm:bottom-4 dark:border-white/10 dark:bg-zinc-900/70 dark:shadow-[0_8px_32px_rgba(0,0,0,0.5)]">
        <div class="flex items-center gap-2">
            @if ($step > 1)
                <button type="button" wire:click="previousStep" class="flex-1 rounded-xl border border-white/50 bg-white/30 px-3 py-2.5 text-sm font-semibold text-gray-800 shadow-sm backdrop-blur-xl hover:bg-white/50 dark:border-white/15 dark:bg-white/5 dark:text-gray-100 dark:hover:bg-white/10">
                    {{ __('app.simple_mode.prev') }}
                </button>
            @endif

            @if ($step < 3)
                <button type="button" wire:click="nextStep" class="flex-1 rounded-xl border border-white/30 bg-orange-600/80 px-3 py-2.5 text-sm font-bold text-white shadow-lg shadow-orange-900/30 backdrop-blur-xl hover:bg-orange-600/95">
                    {{ __('app.simple_mode.next') }}
                </button>
            @else
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="flex-1 rounded-xl border border-white/30 bg-emerald-600/80 px-3 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 backdrop-blur-xl hover:bg-emerald-600/95 disabled:cursor-not-allowed disabled:bg-gray-400/40">
                    <span wire:loading.remove wire:target="save">{{ __('app.simple_mode.move_tenant_in') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.simple_mode.saving') }}</span>
                </button>
            @endif
        </div>
    </div>
</div>
