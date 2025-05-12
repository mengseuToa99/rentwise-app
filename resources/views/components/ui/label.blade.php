@props([
    'for' => null,
    'error' => false
])

<label 
    for="{{ $for }}"
    {{ $attributes->class([
        'block text-sm font-medium text-gray-900 mb-1',
        'text-red-500' => $error,
    ]) }}
>
    {{ $slot }}
</label> 