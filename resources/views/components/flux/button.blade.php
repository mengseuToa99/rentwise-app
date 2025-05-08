@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'rounded' => 'md',
    'icon' => null,
    'iconPosition' => 'right',
    'class' => '',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors';
    
    $variantClasses = [
        'primary' => 'bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-500',
        'secondary' => 'bg-zinc-200 text-zinc-800 hover:bg-zinc-300 focus:ring-zinc-500 dark:bg-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-600',
        'success' => 'bg-green-500 text-white hover:bg-green-600 focus:ring-green-500',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-500',
        'warning' => 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500',
        'info' => 'bg-sky-500 text-white hover:bg-sky-600 focus:ring-sky-500',
        'light' => 'bg-white text-zinc-800 hover:bg-zinc-100 focus:ring-zinc-500 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-700',
        'dark' => 'bg-zinc-800 text-white hover:bg-zinc-900 focus:ring-zinc-500 dark:bg-zinc-700 dark:hover:bg-zinc-600',
        'link' => 'text-blue-500 hover:text-blue-600 focus:ring-blue-500 underline',
        'outline' => 'border border-zinc-300 text-zinc-800 hover:bg-zinc-100 focus:ring-zinc-500 dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-700',
        'none' => '',
    ];
    
    $sizeClasses = [
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-2 py-1 text-sm',
        'md' => 'px-3 py-2 text-sm',
        'lg' => 'px-4 py-2 text-base',
        'xl' => 'px-6 py-3 text-base',
    ];
    
    $roundedClasses = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        'full' => 'rounded-full',
    ];
    
    $iconClasses = $icon ? ($slot->isEmpty() ? '' : ($iconPosition === 'left' ? 'mr-2' : 'ml-2')) : '';
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size] . ' ' . $roundedClasses[$rounded] . ' ' . $class]) }}
>
    @if ($icon && $iconPosition === 'left')
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 {{ $iconClasses }}">
            @switch($icon)
                @case('plus')
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    @break
                @case('paper-airplane')
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    @break
                @default
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
            @endswitch
        </svg>
    @endif
    
    {{ $slot }}
    
    @if ($icon && $iconPosition === 'right')
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 {{ $iconClasses }}">
            @switch($icon)
                @case('plus')
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    @break
                @case('paper-airplane')
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    @break
                @default
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
            @endswitch
        </svg>
    @endif
</button> 