<div class="min-h-screen bg-stone-50 dark:bg-zinc-950 py-4">
    @php
        $authUser = auth()->user();
        $isLandlord = $authUser?->roles->contains(fn ($r) => strtolower($r->role_name) === 'landlord') ?? false;
        $isTenant = $authUser?->roles->contains(fn ($r) => strtolower($r->role_name) === 'tenant') ?? false;
    @endphp

    <div class="mx-auto max-w-3xl px-4 sm:px-6">

        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.title') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.subtitle') }}</p>
            </div>
            <button
                type="button"
                wire:click="exitSimpleMode"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-300 dark:hover:bg-zinc-800"
            >
                {{ __('app.simple_mode.exit') }}
            </button>
        </div>

        {{-- ================================================== --}}
        {{-- TENANT VIEW                                        --}}
        {{-- ================================================== --}}
        @if ($isTenant && !$isLandlord)
            <div class="mb-6">
                <h2 class="mb-3 px-1 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.section_for_you') }}</h2>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <a
                        href="{{ route('tenant.invoices') }}"
                        wire:navigate
                        class="group flex items-center gap-4 rounded-2xl border border-teal-200 bg-white p-5 shadow-sm transition hover:border-teal-400 hover:shadow-md dark:border-teal-900/50 dark:bg-zinc-900 dark:hover:border-teal-700"
                    >
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-teal-600 text-white shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.tile_my_invoices') }}</div>
                            <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.tile_my_invoices_desc') }}</div>
                        </div>
                    </a>

                    <a
                        href="{{ route('tenant.property') }}"
                        wire:navigate
                        class="group flex items-center gap-4 rounded-2xl border border-indigo-200 bg-white p-5 shadow-sm transition hover:border-indigo-400 hover:shadow-md dark:border-indigo-900/50 dark:bg-zinc-900 dark:hover:border-indigo-700"
                    >
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.tile_my_property') }}</div>
                            <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.tile_my_property_desc') }}</div>
                        </div>
                    </a>

                    <a
                        href="{{ route('maintenance.index') }}"
                        wire:navigate
                        class="group flex items-center gap-4 rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition hover:border-amber-400 hover:shadow-md dark:border-amber-900/50 dark:bg-zinc-900 dark:hover:border-amber-700"
                    >
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.tile_maintenance') }}</div>
                            <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.tile_maintenance_desc') }}</div>
                        </div>
                    </a>

                    <a
                        href="{{ route('chat') }}"
                        wire:navigate
                        class="group flex items-center gap-4 rounded-2xl border border-blue-200 bg-white p-5 shadow-sm transition hover:border-blue-400 hover:shadow-md dark:border-blue-900/50 dark:bg-zinc-900 dark:hover:border-blue-700"
                    >
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.tile_chat') }}</div>
                            <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.tile_chat_desc') }}</div>
                        </div>
                    </a>
                </div>
            </div>
        @endif

        {{-- ================================================== --}}
        {{-- LANDLORD VIEW                                      --}}
        {{-- ================================================== --}}
        @if ($isLandlord)
        {{-- Section: Make Invoice --}}
        <div class="mb-6">
            <h2 class="mb-3 px-1 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.section_make_invoice') }}</h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <a
                    href="{{ route('invoices.batch') }}"
                    wire:navigate
                    class="group flex items-center gap-4 rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition hover:border-emerald-400 hover:shadow-md dark:border-emerald-900/50 dark:bg-zinc-900 dark:hover:border-emerald-700"
                >
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.new_invoice') }}</div>
                        <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.new_invoice_desc') }}</div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Section: Property Setup --}}
        <div class="mb-6">
            <h2 class="mb-3 px-1 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.section_property_setup') }}</h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <a
                    href="{{ route('simple-mode.move-out') }}"
                    wire:navigate
                    class="group flex items-center gap-4 rounded-2xl border border-rose-200 bg-white p-5 shadow-sm transition hover:border-rose-400 hover:shadow-md dark:border-rose-900/50 dark:bg-zinc-900 dark:hover:border-rose-700"
                >
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-rose-600 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.move_out') }}</div>
                        <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.move_out_desc') }}</div>
                    </div>
                </a>

                <a
                    href="{{ route('simple-mode.add-tenant') }}"
                    wire:navigate
                    class="group flex items-center gap-4 rounded-2xl border border-orange-200 bg-white p-5 shadow-sm transition hover:border-orange-400 hover:shadow-md dark:border-orange-900/50 dark:bg-zinc-900 dark:hover:border-orange-700"
                >
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-orange-600 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.add_tenant') }}</div>
                        <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.add_tenant_desc') }}</div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Section: Daily Tasks --}}
        <div class="mb-6">
            <h2 class="mb-3 px-1 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.section_daily_tasks') }}</h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <a
                    href="{{ route('utilities.usage.cards') }}"
                    wire:navigate
                    class="group flex items-center gap-4 rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition hover:border-amber-400 hover:shadow-md dark:border-amber-900/50 dark:bg-zinc-900 dark:hover:border-amber-700"
                >
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.meter_readings') }}</div>
                        <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.meter_readings_desc') }}</div>
                    </div>
                </a>

                <a
                    href="{{ route('simple-mode.invoices') }}"
                    wire:navigate
                    class="group flex items-center gap-4 rounded-2xl border border-teal-200 bg-white p-5 shadow-sm transition hover:border-teal-400 hover:shadow-md dark:border-teal-900/50 dark:bg-zinc-900 dark:hover:border-teal-700"
                >
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-teal-600 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.check_invoices') }}</div>
                        <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.check_invoices_desc') }}</div>
                    </div>
                </a>

                <a
                    href="{{ route('simple-mode.mark-paid') }}"
                    wire:navigate
                    class="group flex items-center gap-4 rounded-2xl border border-green-200 bg-white p-5 shadow-sm transition hover:border-green-400 hover:shadow-md dark:border-green-900/50 dark:bg-zinc-900 dark:hover:border-green-700"
                >
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-green-600 text-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ __('app.simple_mode.pay_tile') }}</div>
                        <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('app.simple_mode.pay_tile_desc') }}</div>
                    </div>
                </a>
            </div>
        </div>

        @endif {{-- isLandlord --}}

        <div class="mt-8 rounded-2xl border border-dashed border-gray-300 bg-white/60 p-4 text-center text-sm text-gray-500 dark:border-zinc-700 dark:bg-zinc-900/40 dark:text-gray-400">
            {!! __('app.simple_mode.full_menu_hint', ['exit' => '<strong>' . __('app.simple_mode.exit') . '</strong>']) !!}
        </div>
    </div>
</div>
