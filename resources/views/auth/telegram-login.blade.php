@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Login with Telegram</h2>
            
            <div class="flex justify-center mb-6">
                <!-- Telegram Login Widget -->
                <div id="telegram-login-container" class="flex justify-center">
                    <script async src="https://telegram.org/js/telegram-widget.js?22" 
                            data-telegram-login="{{ config('services.telegram.bot', env('TELEGRAM_BOT_ID')) }}" 
                            data-size="large" 
                            data-auth-url="{{ route('telegram.verify.widget') }}"
                            data-request-access="write"></script>
                </div>
            </div>
            
            <div class="text-center">
                <p class="text-gray-600 mb-4">Click the button above to log in with your Telegram account.</p>
                <p id="status-message" class="text-sm text-gray-500"></p>
            </div>
            
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">
                    Back to login options
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Backup approach using JS callback if data-auth-url doesn't work
    function onTelegramAuth(user) {
        document.getElementById('status-message').textContent = 'Authenticating...';
        
        // Send the authentication data to our server
        fetch('{{ route("telegram.verify.widget") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(user)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('status-message').textContent = 'Login successful. Redirecting...';
                window.location.href = data.redirect || '{{ route("dashboard") }}';
            } else {
                document.getElementById('status-message').textContent = 'Login failed: ' + (data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('status-message').textContent = 'An error occurred during login.';
        });
    }
</script>
@endsection 