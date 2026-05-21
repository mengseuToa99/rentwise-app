<div class="min-h-screen bg-stone-50 dark:bg-zinc-950 pb-32">
    <div class="mx-auto max-w-2xl px-4 pt-4 sm:px-6">

        <div class="mb-5 flex items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.add_room') }}</h1>
            <a
                href="{{ route('simple-mode.home') }}"
                wire:navigate
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
            >
                {{ __('app.simple_mode.back') }}
            </a>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-base text-red-700 dark:bg-red-900/20 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit="save" class="space-y-4">

            {{-- Property --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.which_property') }}</label>
                <select
                    wire:model="propertyId"
                    class="block w-full rounded-xl border-gray-300 px-4 py-3 text-base shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                    <option value="">{{ __('app.simple_mode.pick_property') }}</option>
                    @foreach ($properties as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('propertyId') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </section>

            {{-- Floor --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.floor') }}</label>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="adjustFloor(-1)" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border-2 border-gray-300 bg-white text-2xl font-bold text-gray-700 active:bg-gray-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-200 dark:active:bg-zinc-800">−</button>
                    <div class="flex h-14 w-full items-center justify-center rounded-xl border-2 border-gray-300 bg-stone-50 text-2xl font-bold text-gray-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        {{ __('app.simple_mode.floor_n', ['n' => $floorNumber]) }}
                    </div>
                    <button type="button" wire:click="adjustFloor(1)" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border-2 border-gray-300 bg-white text-2xl font-bold text-gray-700 active:bg-gray-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-200 dark:active:bg-zinc-800">+</button>
                </div>
            </section>

            {{-- Room number --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.room_number') }}</label>
                <input
                    type="text"
                    wire:model="roomNumber"
                    placeholder="{{ __('app.simple_mode.room_number_placeholder') }}"
                    class="block w-full rounded-xl border-gray-300 px-4 py-3 text-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                @error('roomNumber') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </section>

            {{-- Type --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.room_type') }}</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['studio' => __('app.simple_mode.studio'), 'one_bedroom' => __('app.simple_mode.one_bedroom'), 'two_bedroom' => __('app.simple_mode.two_bedroom'), 'three_bedroom' => __('app.simple_mode.three_bedroom')] as $value => $label)
                        <button
                            type="button"
                            wire:click="$set('roomType', '{{ $value }}')"
                            class="rounded-xl border-2 px-3 py-3 text-sm font-semibold {{ $roomType === $value ? 'border-blue-600 bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300' : 'border-gray-300 bg-white text-gray-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </section>

            {{-- Rent --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('app.simple_mode.monthly_rent') }}</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-bold text-gray-500 dark:text-gray-400">$</span>
                    <input
                        type="number"
                        inputmode="decimal"
                        step="0.01"
                        min="0"
                        wire:model="rentAmount"
                        placeholder="0.00"
                        class="block w-full rounded-xl border-gray-300 pl-9 pr-4 py-3 text-lg font-bold shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                </div>
                @error('rentAmount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </section>
        </form>
    </div>

    {{-- Floating liquid-glass save bar --}}
    <div class="fixed bottom-3 left-1/2 z-30 w-[calc(100%-1.5rem)] max-w-2xl -translate-x-1/2 rounded-2xl border border-white/40 bg-white/70 px-3 py-2.5 shadow-[0_8px_32px_rgba(0,0,0,0.12)] backdrop-blur-2xl backdrop-saturate-150 sm:bottom-4 dark:border-white/10 dark:bg-zinc-900/70 dark:shadow-[0_8px_32px_rgba(0,0,0,0.5)]">
        <button
            type="button"
            wire:click="save"
            wire:loading.attr="disabled"
            class="w-full rounded-xl border border-white/30 bg-blue-600/75 px-5 py-3 text-base font-bold text-white shadow-lg shadow-blue-900/30 backdrop-blur-xl hover:bg-blue-600/90 disabled:cursor-not-allowed disabled:border-white/10 disabled:bg-gray-400/40 disabled:shadow-none"
        >
            <span wire:loading.remove wire:target="save">{{ __('app.simple_mode.save_room') }}</span>
            <span wire:loading wire:target="save">{{ __('app.simple_mode.saving') }}</span>
        </button>
    </div>
</div>
