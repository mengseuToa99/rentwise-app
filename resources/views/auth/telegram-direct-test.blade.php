<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Basic Telegram Widget Test</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .widget-container {
            margin: 30px 0;
        }
        .debug {
            margin-top: 20px;
            text-align: left;
            font-size: 14px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Basic Telegram Login Test</h1>
    
    <div class="widget-container">
        <!-- Telegram Login Widget -->
        <script async src="https://telegram.org/js/telegram-widget.js?22" 
            data-telegram-login="rentwiseApp_Bot" 
            data-size="large" 
            data-onauth="onTelegramAuth(user)"></script>
    </div>
    
    <div class="debug">
        <p>Debug Info:</p>
        <div>Bot Username: rentwiseApp_Bot</div>
        <div>Widget URL: https://telegram.org/js/telegram-widget.js?22</div>
        <div id="auth-data">Authentication data will appear here</div>
    </div>
    
    <script>
        function onTelegramAuth(user) {
            alert('Logged in as ' + user.first_name + ' ' + user.last_name + ' (' + user.id + (user.username ? ', @' + user.username : '') + ')');
            document.getElementById('auth-data').innerHTML = '<pre>' + JSON.stringify(user, null, 2) + '</pre>';
            
            console.log(user);
        }
    </script>
    
    <p><a href="{{ route('login') }}">Back to login page</a></p>
</body>
</html> 