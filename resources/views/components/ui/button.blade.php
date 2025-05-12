@props([
    'variant' => 'default',
    'size' => 'default',
    'type' => 'button',
    'disabled' => false,
])

@php
    $variantClasses = [
        'default' => 'bg-indigo-600 text-white shadow hover:bg-indigo-500',
        'destructive' => 'bg-red-600 text-white shadow-sm hover:bg-red-500',
        'outline' => 'border border-gray-300 bg-white shadow-sm hover:bg-gray-100 hover:text-gray-900',
        'secondary' => 'bg-gray-200 text-gray-900 shadow-sm hover:bg-gray-100',
        'ghost' => 'hover:bg-gray-100 hover:text-gray-900',
        'link' => 'text-indigo-600 underline-offset-4 hover:underline',
    ];
    
    $sizeClasses = [
        'default' => 'h-9 px-4 py-2',
        'sm' => 'h-8 rounded-md px-3 text-xs',
        'lg' => 'h-10 rounded-md px-8',
        'icon' => 'h-9 w-9',
    ];
@endphp

<button
    type="{{ $type }}"
    @disabled($disabled)
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50',
        $variantClasses[$variant] ?? $variantClasses['default'],
        $sizeClasses[$size] ?? $sizeClasses['default'],
    ]) }}
>
    {{ $slot }}
</button> 