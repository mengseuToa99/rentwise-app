<div>
    <button
        wire:click="switchLanguage"
        class="flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
        title="{{ $currentLocale === 'en' ? __('app.switch_to_khmer') : __('app.switch_to_english') }}"
    >
        @if($currentLocale === 'en')
            <span class="text-sm font-medium">{{ __('app.khmer') }}</span>
        @else
            <span class="text-sm font-medium">{{ __('app.english') }}</span>
        @endif
    </button>
</div>
