@props([
    'type' => 'text',
    'placeholder' => '',
    'disabled' => false,
    'error' => false
])

<input 
    type="{{ $type }}"
    placeholder="{{ $placeholder }}"
    @disabled($disabled)
    {{ $attributes->class([
        'flex h-9 w-full rounded-md border bg-transparent px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
        'border-red-500' => $error,
        'border-gray-300' => !$error,
    ])->merge(['class' => 'ring-gray-300 focus-visible:ring-indigo-600']) }}
> 