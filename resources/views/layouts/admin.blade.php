<x-layouts.app.sidebar preserveSidebar="true">
    {{ $slot }}

    @unless(auth()->user()->roles->contains(function($role) {
        return strtolower($role->role_name) === 'admin';
    }))
        <script>
            window.location.href = '{{ route('dashboard') }}';
        </script>
    @endunless
</x-layouts.app.sidebar>
