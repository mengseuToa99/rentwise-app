<div class="py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('app.meter_reading_cards.title') }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('app.meter_reading_cards.subtitle') }}
                </p>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <a
                    href="{{ route('utilities.usage', ['selectedProperty' => $selectedProperty, 'selectedUtility' => $selectedUtility, 'selectedYear' => $selectedYear, 'selectedMonth' => $selectedMonth]) }}"
                    wire:navigate
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    {{ __('app.meter_reading_cards.table_view') }}
                </a>
                @if (session('simple_mode'))
                    <a
                        href="{{ route('simple-mode.home') }}"
                        wire:navigate
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
                    >
                        {{ __('app.utility_usage_history.back') }}
                    </a>
                @endif
            </div>
        </div>

        <!-- Export bar -->
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <span class="mr-1 text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.meter_reading_cards.export_as') }}</span>
            <a
                href="{{ route('utilities.usage.excel', $exportParams) }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('app.meter_reading_cards.excel') }}
            </a>
            <a
                href="{{ route('utilities.usage.pdf', $exportParams) }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('app.meter_reading_cards.pdf') }}
            </a>
            <a
                href="{{ route('utilities.usage.csv', $exportParams) }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ __('app.meter_reading_cards.csv') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 sm:p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="property" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.utility_usage_history.property') }}</label>
                    <select wire:model.live="selectedProperty" id="property"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">{{ __('app.utility_usage_history.all_properties') }}</option>
                        @foreach($properties as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="utility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.utility_usage_history.utility_type') }}</label>
                    <select wire:model.live="selectedUtility" id="utility"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">{{ __('app.utility_usage_history.all_utilities') }}</option>
                        @foreach($utilities as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.utility_usage_history.year') }}</label>
                    <select wire:model.live="selectedYear" id="year"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('app.utility_usage_history.month') }}</label>
                    <select wire:model.live="selectedMonth" id="month"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach($months as $value => $name)
                            <option value="{{ $value }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div wire:loading.class="opacity-50" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($usages as $room)
                <div class="flex flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="text-base font-bold text-gray-900 dark:text-white">{{ $room['property_name'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.utility_usage_history.room_n', ['n' => $room['room_number']]) }}</div>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                            {{ __('app.meter_reading_cards.readings_count', ['count' => $room['readings']->count()]) }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $room['tenant'] }}
                    </div>

                    <!-- One row per utility reading in this room -->
                    <div class="mt-4 space-y-3 border-t border-dashed border-gray-200 pt-4 dark:border-gray-700">
                        @foreach($room['readings'] as $usage)
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $usage->utility_name }}</span>
                                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($usage->calculateCharge(), 2) }}</span>
                                </div>
                                <div class="mt-1 flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $usage->usage_date->format('M d, Y') }}
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                                    <span>{{ number_format($usage->old_meter_reading, 2) }} &rarr; {{ number_format($usage->new_meter_reading, 2) }}</span>
                                    <span class="font-medium">{{ __('app.utility_usage_history.n_units', ['n' => number_format($usage->amount_used, 2)]) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Room total -->
                    <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-3 dark:border-gray-700">
                        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('app.meter_reading_cards.total') }}</span>
                        <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($room['total_charge'], 2) }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white/60 p-10 text-center text-sm text-gray-500 dark:border-zinc-700 dark:bg-zinc-900/40 dark:text-gray-400">
                    {{ __('app.utility_usage_history.no_records') }}
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $usages->links() }}
        </div>
    </div>
</div>
