@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex justify-between items-center px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 ' . $class]) }}>
    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
        {{ $slot }}
    </h3>
    <button type="button" @click="$dispatch('close')" class="text-zinc-400 hover:text-zinc-500 dark:hover:text-zinc-300">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div> 