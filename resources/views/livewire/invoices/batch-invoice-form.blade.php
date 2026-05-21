<div class="min-h-screen bg-stone-50 pb-24 dark:bg-zinc-950">
    @php
        $selectedCount = count($selectedRentalIds);
        $totalRentals = count($rentals);
        $utilityCols = $this->utilityColumns;
        // Build alpine state seed for stage 2
        $alpineSeed = [];
        foreach ($selectedRentalIds as $rid) {
            $row = [];
            foreach ($roomReadings[$rid] ?? [] as $uid => $r) {
                $row[(string) $uid] = [
                    'val' => (string) $r['new_reading'],
                    'prev' => (float) $r['previous_reading'],
                    'rate' => (float) $r['rate'],
                ];
            }
            $alpineSeed[(string) $rid] = $row;
        }
    @endphp

    {{-- Header --}}
    <header class="sticky top-0 z-30 border-b border-gray-200 bg-stone-50/95 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/95">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <a
                    href="{{ route(session('simple_mode') ? 'simple-mode.home' : 'invoices.index') }}"
                    wire:navigate
                    class="inline-flex shrink-0 items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                    title="{{ __('app.batch_invoice.back_header') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="min-w-0">
                    <h1 class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ __('app.batch_invoice.title') }}</h1>
                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                        @if ($stage === 1)
                            {{ __('app.batch_invoice.pick_rooms') }}
                        @else
                            {{ __('app.batch_invoice.enter_readings_count', ['count' => $selectedCount, 'unit' => $selectedCount === 1 ? __('app.batch_invoice.rooms_one') : __('app.batch_invoice.rooms_many')]) }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Mini-stepper, only when relevant --}}
            <div class="hidden items-center gap-1.5 text-xs font-medium sm:flex">
                <span class="flex items-center gap-1.5 {{ $stage === 1 ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full {{ $stage === 1 ? 'bg-blue-600 text-white' : 'bg-emerald-500 text-white' }}">
                        @if ($stage > 1)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            1
                        @endif
                    </span>
                    {{ __('app.batch_invoice.step_select') }}
                </span>
                <div class="h-px w-8 bg-gray-200 dark:bg-zinc-700"></div>
                <span class="flex items-center gap-1.5 {{ $stage >= 2 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full {{ $stage >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500 dark:bg-zinc-800 dark:text-gray-400' }}">2</span>
                    {{ __('app.batch_invoice.step_fill') }}
                </span>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6">

        @if (session('error'))
            <div class="mb-4 flex items-start gap-2 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 flex items-start gap-2 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- ========================================================== --}}
        {{-- STAGE 1 — Select rooms                                    --}}
        {{-- ========================================================== --}}
        @if ($stage === 1)
            @php $propertyList = $this->properties; $needsPropertyPick = $propertyList->count() > 1 && $selectedPropertyId === null; @endphp

            @if ($needsPropertyPick)
                {{-- Property picker first --}}
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('app.batch_invoice.pick_property_first') }}</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('app.batch_invoice.pick_property_hint') }}</p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($propertyList as $prop)
                        <button
                            type="button"
                            wire:click="selectProperty('{{ $prop['property_id'] }}')"
                            class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition hover:border-blue-500 hover:bg-blue-50 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-500 dark:hover:bg-blue-950/30"
                        >
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-base font-bold text-gray-900 dark:text-white">{{ $prop['property_name'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('app.batch_invoice.rooms_count', ['count' => $prop['room_count']]) }}</div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endforeach
                </div>
            @else
                {{-- Selected property chip --}}
                @if ($selectedPropertyId && $propertyList->count() > 1)
                    @php $currentProp = $propertyList->firstWhere('property_id', $selectedPropertyId); @endphp
                    @if ($currentProp)
                        <div class="mb-3 flex items-center justify-between gap-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 dark:border-blue-900/50 dark:bg-blue-950/30">
                            <div class="flex min-w-0 items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"/></svg>
                                <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $currentProp['property_name'] }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">· {{ __('app.batch_invoice.rooms_count', ['count' => $currentProp['room_count']]) }}</span>
                            </div>
                            <button type="button" wire:click="clearProperty" class="shrink-0 rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800">
                                {{ __('app.batch_invoice.change_property') }}
                            </button>
                        </div>
                    @endif
                @endif

                <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 110-16 8 8 0 010 16z"/></svg>
                        <input
                            type="text"
                            wire:model.live.debounce.250ms="search"
                            placeholder="{{ __('app.batch_invoice.search_placeholder') }}"
                            class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-9 pr-3 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:placeholder:text-gray-500"
                        >
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('app.batch_invoice.count_of_total', ['shown' => $this->filteredRentals->count(), 'total' => $totalRentals]) }}</span>
                        <button type="button" wire:click="selectAll" class="font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">{{ __('app.batch_invoice.select_all') }}</button>
                        @if ($selectedCount > 0)
                            <button type="button" wire:click="clearAll" class="font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">{{ __('app.batch_invoice.clear') }}</button>
                        @endif
                    </div>
                </div>

                <ul class="divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-sm dark:divide-zinc-800 dark:border-zinc-800 dark:bg-zinc-900">
                    @forelse ($this->filteredRentals as $rental)
                        @php $isSelected = in_array($rental['rental_id'], $selectedRentalIds, true); @endphp
                        <li>
                            <button
                                type="button"
                                wire:click="toggleRental('{{ $rental['rental_id'] }}')"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-left hover:bg-gray-50 dark:hover:bg-zinc-800/60 {{ $isSelected ? 'bg-blue-50/50 dark:bg-blue-950/20' : '' }}"
                            >
                                <div class="flex h-4 w-4 shrink-0 items-center justify-center rounded border-2 {{ $isSelected ? 'border-blue-600 bg-blue-600' : 'border-gray-300 dark:border-zinc-600' }}">
                                    @if ($isSelected)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </div>
                                <div class="flex h-8 w-10 shrink-0 items-center justify-center rounded-md bg-blue-600 text-[11px] font-bold text-white">
                                    @if ($rental['floor_number'] !== null)F{{ $rental['floor_number'] }}@else —@endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $rental['room_label'] }}</div>
                                    <div class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $rental['property_name'] }}</div>
                                </div>
                                <div class="hidden shrink-0 truncate text-xs text-gray-500 dark:text-gray-400 sm:block">{{ $rental['tenant_name'] }}</div>
                            </button>
                        </li>
                    @empty
                        <li class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('app.batch_invoice.no_tenants_match') }}</li>
                    @endforelse
                </ul>
            @endif
        @endif

        {{-- ========================================================== --}}
        {{-- STAGE 2 — Spreadsheet: fill readings & create              --}}
        {{-- ========================================================== --}}
        @if ($stage === 2 && $selectedCount > 0)
            <div
                x-data="{
                    rooms: @js($alpineSeed),
                    used(rid, uid) {
                        const r = this.rooms[rid]?.[uid];
                        if (!r) return 0;
                        const n = parseFloat(r.val);
                        if (isNaN(n)) return 0;
                        return Math.max(0, n - r.prev);
                    },
                    charge(rid, uid) {
                        const r = this.rooms[rid]?.[uid];
                        if (!r) return 0;
                        return this.used(rid, uid) * r.rate;
                    },
                    hasValue(rid, uid) {
                        const r = this.rooms[rid]?.[uid];
                        if (!r) return false;
                        return r.val !== '' && !isNaN(parseFloat(r.val));
                    },
                    roomTotal(rid) {
                        let t = 0;
                        for (const uid in this.rooms[rid] || {}) t += this.charge(rid, uid);
                        return t;
                    },
                    roomHasAny(rid) {
                        for (const uid in this.rooms[rid] || {}) if (this.hasValue(rid, uid)) return true;
                        return false;
                    },
                    get total() {
                        let t = 0;
                        for (const rid in this.rooms) t += this.roomTotal(rid);
                        return t;
                    },
                    get readyCount() {
                        let c = 0;
                        for (const rid in this.rooms) if (this.roomHasAny(rid)) c++;
                        return c;
                    },
                    fmt(n) { return n.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); },
                    same(rid, uid) {
                        this.rooms[rid][uid].val = this.rooms[rid][uid].prev.toString();
                        $wire.set(`roomReadings.${rid}.${uid}.new_reading`, this.rooms[rid][uid].val);
                    },
                }"
                class="space-y-3"
            >
                {{-- Bulk default due date strip --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-xs dark:border-zinc-800 dark:bg-zinc-900/50">
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('app.batch_invoice.default_due') }}</span>
                    <input
                        type="date"
                        wire:model.live="defaultDueDate"
                        class="rounded border border-gray-300 bg-white px-2 py-1 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                    <button type="button" wire:click="setBatchDueDate('7_days')" class="rounded border border-gray-300 bg-white px-2 py-1 font-medium text-gray-700 hover:bg-gray-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-300 dark:hover:bg-zinc-700">{{ __('app.batch_invoice.plus_7d') }}</button>
                    <button type="button" wire:click="setBatchDueDate('15_days')" class="rounded border border-gray-300 bg-white px-2 py-1 font-medium text-gray-700 hover:bg-gray-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-300 dark:hover:bg-zinc-700">{{ __('app.batch_invoice.plus_15d') }}</button>
                    <button type="button" wire:click="setBatchDueDate('month_end')" class="rounded border border-gray-300 bg-white px-2 py-1 font-medium text-gray-700 hover:bg-gray-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-300 dark:hover:bg-zinc-700">{{ __('app.batch_invoice.month_end') }}</button>
                    <span class="ml-auto text-gray-500 dark:text-gray-400">{{ __('app.batch_invoice.applies_to_all') }}</span>
                </div>

                {{-- DESKTOP TABLE --}}
                <div class="hidden overflow-hidden rounded-lg border border-gray-200 dark:border-zinc-800 lg:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-zinc-900">
                                <tr class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    <th class="sticky left-0 z-10 bg-gray-50 px-3 py-2 dark:bg-zinc-900">{{ __('app.batch_invoice.col_tenant_room') }}</th>
                                    @foreach ($utilityCols as $col)
                                        <th class="px-3 py-2 text-right">{{ $col['utility_name'] }}</th>
                                    @endforeach
                                    <th class="px-3 py-2">{{ __('app.batch_invoice.col_due') }}</th>
                                    <th class="px-3 py-2 text-right">{{ __('app.batch_invoice.col_total') }}</th>
                                    <th class="px-2 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white dark:divide-zinc-800 dark:bg-zinc-950">
                                @foreach ($selectedRentalIds as $idx => $rid)
                                    @php
                                        $room = collect($rentals)->firstWhere('rental_id', $rid);
                                        $readings = $roomReadings[$rid] ?? [];
                                        $dueDate = $roomDueDates[$rid] ?? $defaultDueDate;
                                    @endphp
                                    @if ($room)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-900/40">
                                            {{-- Tenant cell (sticky) --}}
                                            <td class="sticky left-0 z-10 bg-white px-3 py-2 group-hover:bg-gray-50 dark:bg-zinc-950">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex h-8 w-10 shrink-0 items-center justify-center rounded-md bg-blue-600 text-[11px] font-bold text-white">
                                                        @if ($room['floor_number'] !== null)F{{ $room['floor_number'] }}@else —@endif
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $room['room_label'] }}</div>
                                                        <div class="truncate text-[11px] text-gray-500 dark:text-gray-400">{{ $room['property_name'] }} · {{ $room['tenant_name'] }}</div>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Utility cells --}}
                                            @foreach ($utilityCols as $col)
                                                @php
                                                    $uid = $col['utility_id'];
                                                    $reading = $readings[$uid] ?? null;
                                                @endphp
                                                <td class="px-3 py-2 align-top">
                                                    @if ($reading)
                                                        <div class="flex items-center justify-end gap-1">
                                                            <input
                                                                type="number"
                                                                inputmode="decimal"
                                                                step="0.01"
                                                                min="{{ $reading['previous_reading'] }}"
                                                                x-model="rooms['{{ $rid }}']['{{ $uid }}'].val"
                                                                wire:model.blur="roomReadings.{{ $rid }}.{{ $uid }}.new_reading"
                                                                class="block h-8 w-24 rounded border border-gray-300 bg-white px-2 text-right text-sm font-semibold tabular-nums shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                                                                placeholder="—"
                                                            >
                                                            <button
                                                                type="button"
                                                                x-on:click="same('{{ $rid }}', '{{ $uid }}')"
                                                                title="{{ __('app.batch_invoice.same_as_previous', ['value' => number_format($reading['previous_reading'], 2)]) }}"
                                                                class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                            </button>
                                                        </div>
                                                        <div class="mt-1 flex items-center justify-end gap-1 text-[10px] text-gray-500 dark:text-gray-400">
                                                            <span>{{ __('app.batch_invoice.prev', ['value' => number_format($reading['previous_reading'], 2)]) }}</span>
                                                            <span class="text-gray-300 dark:text-zinc-600">·</span>
                                                            <span x-show="hasValue('{{ $rid }}','{{ $uid }}')" class="font-semibold text-emerald-600 dark:text-emerald-400" style="display:none">
                                                                $<span x-text="fmt(charge('{{ $rid }}','{{ $uid }}'))"></span>
                                                            </span>
                                                            <span x-show="!hasValue('{{ $rid }}','{{ $uid }}')" class="text-gray-300 dark:text-zinc-600">${{ number_format($reading['rate'], 2) }}/{{ $reading['unit_of_measure'] }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-300 dark:text-zinc-600">—</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            {{-- Due date --}}
                                            <td class="px-3 py-2 align-top">
                                                <input
                                                    type="date"
                                                    wire:model.blur="roomDueDates.{{ $rid }}"
                                                    value="{{ $dueDate }}"
                                                    class="block h-8 w-36 rounded border border-gray-300 bg-white px-2 text-xs shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                                                >
                                            </td>

                                            {{-- Row total --}}
                                            <td class="px-3 py-2 text-right align-top">
                                                <div class="text-base font-semibold tabular-nums" :class="roomHasAny('{{ $rid }}') ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-zinc-700'">
                                                    $<span x-text="fmt(roomTotal('{{ $rid }}'))">0.00</span>
                                                </div>
                                            </td>

                                            {{-- Remove --}}
                                            <td class="px-2 py-2 align-top">
                                                <button
                                                    type="button"
                                                    wire:click="removeRoom('{{ $rid }}')"
                                                    wire:confirm="{{ __('app.batch_invoice.remove_confirm') }}"
                                                    class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400"
                                                    title="{{ __('app.batch_invoice.remove') }}"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-zinc-900">
                                <tr class="text-sm">
                                    <td class="sticky left-0 z-10 bg-gray-50 px-3 py-2.5 font-semibold text-gray-700 dark:bg-zinc-900 dark:text-gray-300" colspan="{{ count($utilityCols) + 2 }}">
                                        <span x-text="readyCount">0</span>{{ __('app.batch_invoice.ready_of', ['total' => $selectedCount]) }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right text-base font-bold tabular-nums text-gray-900 dark:text-white">
                                        $<span x-text="fmt(total)">0.00</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- MOBILE: stacked cards --}}
                <div class="space-y-3 lg:hidden">
                    @foreach ($selectedRentalIds as $idx => $rid)
                        @php
                            $room = collect($rentals)->firstWhere('rental_id', $rid);
                            $readings = $roomReadings[$rid] ?? [];
                            $dueDate = $roomDueDates[$rid] ?? $defaultDueDate;
                        @endphp
                        @if ($room)
                            <div class="rounded-lg border border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="flex items-center justify-between gap-2 border-b border-gray-100 px-3 py-2 dark:border-zinc-800">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <div class="flex h-8 w-10 shrink-0 items-center justify-center rounded-md bg-blue-600 text-[11px] font-bold text-white">@if ($room['floor_number'] !== null)F{{ $room['floor_number'] }}@else —@endif</div>
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $room['room_label'] }}</div>
                                            <div class="truncate text-[11px] text-gray-500 dark:text-gray-400">{{ $room['property_name'] }} · {{ $room['tenant_name'] }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold tabular-nums text-gray-900 dark:text-white">$<span x-text="fmt(roomTotal('{{ $rid }}'))">0.00</span></span>
                                        <button type="button" wire:click="removeRoom('{{ $rid }}')" wire:confirm="{{ __('app.batch_invoice.remove_confirm') }}" class="rounded p-1 text-gray-400 hover:text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                                    @foreach ($readings as $uid => $reading)
                                        <div class="flex items-center justify-between gap-3 px-3 py-2">
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $reading['utility_name'] }}</div>
                                                <div class="text-[11px] text-gray-500 dark:text-gray-400">{{ __('app.batch_invoice.prev', ['value' => number_format($reading['previous_reading'], 2)]) }} · ${{ number_format($reading['rate'], 2) }}/{{ $reading['unit_of_measure'] }}</div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <input
                                                    type="number"
                                                    inputmode="decimal"
                                                    step="0.01"
                                                    min="{{ $reading['previous_reading'] }}"
                                                    x-model="rooms['{{ $rid }}']['{{ $uid }}'].val"
                                                    wire:model.blur="roomReadings.{{ $rid }}.{{ $uid }}.new_reading"
                                                    class="block h-9 w-24 rounded border border-gray-300 bg-white px-2 text-right text-sm font-semibold tabular-nums focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                                                    placeholder="—"
                                                >
                                                <button type="button" x-on:click="same('{{ $rid }}', '{{ $uid }}')" class="rounded p-1.5 text-gray-400 hover:text-gray-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="flex items-center justify-between px-3 py-2 text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('app.batch_invoice.due_label') }}</span>
                                        <input
                                            type="date"
                                            wire:model.blur="roomDueDates.{{ $rid }}"
                                            value="{{ $dueDate }}"
                                            class="rounded border border-gray-300 bg-white px-2 py-1 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                                        >
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Bottom sticky action bar --}}
                <div class="fixed inset-x-0 bottom-0 z-30 border-t border-gray-200 bg-white/95 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/95">
                    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
                        <div class="text-sm">
                            <div class="font-semibold text-gray-900 dark:text-white">$<span x-text="fmt(total)">0.00</span></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400"><span x-text="readyCount">0</span>{{ __('app.batch_invoice.ready_of_short', ['total' => $selectedCount]) }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                wire:click="backToPick"
                                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
                            >
                                {{ __('app.batch_invoice.back') }}
                            </button>
                            <button
                                type="button"
                                wire:click="createAll"
                                wire:loading.attr="disabled"
                                x-bind:disabled="readyCount === 0"
                                class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300 dark:disabled:bg-zinc-700"
                            >
                                <span wire:loading.remove wire:target="createAll">{{ __('app.batch_invoice.create_label') }} <span x-text="readyCount">0</span> {{ __('app.batch_invoice.invoice_word') }}<span x-show="readyCount !== 1">{{ __('app.batch_invoice.plural_s') }}</span></span>
                                <span wire:loading wire:target="createAll">{{ __('app.batch_invoice.creating') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Stage 1 sticky action bar — hidden while picking a property --}}
        @if ($stage === 1 && !($this->properties->count() > 1 && $selectedPropertyId === null))
            <div class="fixed inset-x-0 bottom-0 z-30 border-t border-gray-200 bg-white/95 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/95">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
                    <div class="text-sm">
                        <div class="font-semibold text-gray-900 dark:text-white">{{ __('app.batch_invoice.selected_count', ['count' => $selectedCount]) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('app.batch_invoice.default_due_value', ['date' => \Carbon\Carbon::parse($defaultDueDate)->format('d M Y')]) }}</div>
                    </div>
                    <button
                        type="button"
                        wire:click="startFilling"
                        @disabled(empty($selectedRentalIds))
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300 dark:disabled:bg-zinc-700"
                    >
                        {{ __('app.batch_invoice.continue') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        @endif

    </main>
</div>
