<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Telegram Login Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .status {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
            min-height: 60px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .back-link {
            margin-top: 20px;
        }
        .debug-info {
            margin-top: 20px;
            text-align: left;
            font-size: 12px;
            color: #777;
            background-color: #f2f2f2;
            padding: 10px;
            border-radius: 4px;
            overflow-wrap: break-word;
        }
        .debug-info h3 {
            margin-top: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Telegram Login Test</h1>
        
        <div id="telegram-login">
            <!-- Original widget that might be blocked -->
            <script async src="https://telegram.org/js/telegram-widget.js?22" 
                data-telegram-login="{{ env('TELEGRAM_BOT_ID') }}" 
                data-size="large" 
                data-onauth="onTelegramAuth(user)" 
                data-request-access="write"
                onerror="document.getElementById('telegram-login-error').style.display='block'"></script>
            
            <!-- Fallback message if script fails to load -->
            <div id="telegram-login-error" style="display:none; padding: 10px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; margin-top: 10px;">
                <p>Telegram login widget failed to load. This could be due to:</p>
                <ul style="text-align: left; margin-top: 10px;">
                    <li>Content blockers or privacy extensions</li>
                    <li>Network issues connecting to Telegram servers</li>
                    <li>Bot configuration issues</li>
                </ul>
                <p style="margin-top: 10px;">Try disabling content blockers or try again later.</p>
            </div>
            
            <!-- Direct link fallback -->
            <div style="margin-top: 15px;">
                <a href="https://t.me/{{ env('TELEGRAM_BOT_ID') }}?start=auth" 
                   style="display: inline-block; padding: 10px 15px; background-color: #0088cc; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Login with Telegram Bot
                </a>
            </div>
        </div>
        
        <div class="status" id="status">Click the Telegram button above to login</div>
        
        <div class="debug-info">
            <h3>Configuration:</h3>
            <div>Bot ID: {{ env('TELEGRAM_BOT_ID') }}</div>
            <div>Token available: {{ !empty(env('TELEGRAM_TOKEN')) ? 'Yes' : 'No' }}</div>
        </div>
        
        <div class="back-link">
            <a href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>
    
    <script>
        function onTelegramAuth(user) {
            document.getElementById('status').innerText = 'Authenticating...';
            console.log('Telegram auth data:', user);
            
            // Show debug info
            let debugElem = document.createElement('div');
            debugElem.className = 'debug-info';
            debugElem.innerHTML = '<h3>Auth Data:</h3><pre>' + JSON.stringify(user, null, 2) + '</pre>';
            document.querySelector('.debug-info').appendChild(debugElem);
            
            // Send to our backend
            fetch('{{ route("telegram.verify.widget") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(user)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    document.getElementById('status').innerText = 'Login successful! Redirecting...';
                    window.location.href = data.redirect;
                } else {
                    document.getElementById('status').innerText = 'Error: ' + (data.message || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('status').innerText = 'Failed to authenticate: ' + error;
            });
        }
    </script>
</body>
</html> 