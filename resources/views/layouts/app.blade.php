<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-zinc-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RentWise') }}</title>

    @include('partials.theme-init')

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="RentWise">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Styles -->
    @livewireStyles
</head>
<body class="h-full">
    @include('layouts.navigation')

    <main class="py-10">
        {{ $slot }}
    </main>
    
    @livewireScripts
    @stack('scripts')
</body>
</html> 