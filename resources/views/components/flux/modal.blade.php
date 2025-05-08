@props([
    'open' => false,
    'size' => 'md',
    'closeButton' => true,
])

<div 
    x-data="{ 
        open: @entangle($attributes->wire('model')),
        size: '{{ $size }}'
    }" 
    x-show="open" 
    x-on:close.stop="open = false"
    x-on:keydown.escape.window="open = false"
    class="fixed inset-0 z-50 overflow-y-auto" 
    style="display: none;"
>
    <div class="flex min-h-screen items-center justify-center px-4">
        <div 
            x-show="open" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0" 
            class="fixed inset-0 bg-zinc-900/50 transition-opacity"
            @click="open = false"
        ></div>

        <div 
            x-show="open" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
            class="transform rounded-lg bg-white dark:bg-zinc-800 shadow-xl transition-all"
            :class="{
                'max-w-sm': size === 'sm',
                'max-w-md': size === 'md',
                'max-w-lg': size === 'lg',
                'max-w-xl': size === 'xl',
                'max-w-2xl': size === '2xl',
                'max-w-3xl': size === '3xl',
                'max-w-4xl': size === '4xl',
                'max-w-5xl': size === '5xl',
                'max-w-6xl': size === '6xl',
                'max-w-7xl': size === '7xl',
            }"
            @click.stop
        >
            {{ $slot }}
        </div>
    </div>
</div> 