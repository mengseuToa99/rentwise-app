@props([
    'type' => 'text',
    'placeholder' => '',
    'class' => '',
])

<input
    type="{{ $type }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 ' . $class]) }}
> 