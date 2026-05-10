@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-gray-200 bg-white text-gray-900 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 ' . $class]) }}>
    {{ $slot }}
</div> 