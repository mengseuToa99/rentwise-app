<x-layouts.app.sidebar preserveSidebar="true">
    <flux:main>
        {{ $slot }}

        @unless(auth()->user()->roles->contains(function($role) {
            return strtolower($role->role_name) === 'admin';
        }))
            <script>
                window.location.href = '{{ route('dashboard') }}';
            </script>
        @endunless
    </flux:main>
</x-layouts.app.sidebar>
