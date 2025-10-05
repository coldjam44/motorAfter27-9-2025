<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusher Test - Real-time Notifications</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .message-box {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            animation: fadeIn 0.5s ease-in;
        }
        .error-box {
            background: #ffe8e8;
            border: 1px solid #f44336;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .connected { background: #d4edda; color: #155724; }
        .disconnected { background: #f8d7da; color: #721c24; }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover { background: #0056b3; }
        input[type="text"] {
            width: 300px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 5px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        #messages {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚗 Pusher Test - Auction Real-time Demo</h1>
        <p>This page demonstrates real-time communication using Pusher for your auction system.</p>
        
        <div id="connection-status" class="status disconnected">
            📡 Connection Status: Disconnected
        </div>

        <div style="margin: 20px 0;">
            <h3>🔥 Trigger Test Event</h3>
            <input type="text" id="messageInput" placeholder="Enter your test message..." value="New bid placed! $1,250">
            <button onclick="triggerEvent()">Send Test Message</button>
            <button onclick="triggerAuctionEvent()">Simulate Auction Update</button>
        </div>

        <div style="margin: 20px 0;">
            <h3>📨 Real-time Messages</h3>
            <div id="messages">
                <p><em>Waiting for real-time messages...</em></p>
            </div>
            <button onclick="clearMessages()">Clear Messages</button>
        </div>

        <div style="margin: 20px 0;">
            <h3>📋 Instructions</h3>
            <ol>
                <li><strong>Test without Pusher credentials:</strong> Events will be logged in Laravel logs</li>
                <li><strong>To enable real-time:</strong> Add your Pusher credentials to .env file</li>
                <li><strong>Get Pusher credentials:</strong> Sign up at <a href="https://pusher.com" target="_blank">pusher.com</a></li>
                <li><strong>Open multiple tabs:</strong> See messages appear simultaneously</li>
            </ol>
        </div>
    </div>

    <!-- Pusher JS Client -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        let pusher = null;
        let channel = null;

        // Initialize Pusher (will work if credentials are set)
        try {
            pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
                cluster: '{{ env("PUSHER_APP_CLUSTER", "mt1") }}',
                encrypted: true
            });

            // Subscribe to channel
            channel = pusher.subscribe('auction-test');
            
            // Update connection status
            pusher.connection.bind('connected', function() {
                updateConnectionStatus(true);
            });

            pusher.connection.bind('disconnected', function() {
                updateConnectionStatus(false);
            });

            // Listen for auction updates
            channel.bind('auction.update', function(data) {
                displayMessage('🎯 Real-time Event Received!', data.message, data.timestamp);
            });

        } catch (error) {
            console.log('Pusher not configured:', error);
            displayMessage('⚠️ Info', 'Pusher credentials not configured. Events will be logged in Laravel logs.', new Date().toLocaleTimeString());
        }

        function updateConnectionStatus(connected) {
            const statusEl = document.getElementById('connection-status');
            if (connected) {
                statusEl.className = 'status connected';
                statusEl.innerHTML = '✅ Connection Status: Connected to Pusher';
            } else {
                statusEl.className = 'status disconnected';
                statusEl.innerHTML = '❌ Connection Status: Disconnected';
            }
        }

        function triggerEvent() {
            const message = document.getElementById('messageInput').value;
            
            fetch('{{ route("trigger.test.event") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                displayMessage('📤 Event Triggered', data.message, new Date().toLocaleTimeString());
                document.getElementById('messageInput').value = '';
            })
            .catch(error => {
                displayMessage('❌ Error', 'Failed to trigger event: ' + error.message, new Date().toLocaleTimeString(), true);
            });
        }

        function triggerAuctionEvent() {
            const auctionMessages = [
                'New bid placed: $1,250 by User #123',
                'Auction ending in 2 minutes!',
                'Bid increased to $1,500',
                'New participant joined the auction',
                'Counter bid: $1,750',
                'Final call - auction ending soon!'
            ];
            
            const randomMessage = auctionMessages[Math.floor(Math.random() * auctionMessages.length)];
            document.getElementById('messageInput').value = randomMessage;
            triggerEvent();
        }

        function displayMessage(title, message, timestamp, isError = false) {
            const messagesDiv = document.getElementById('messages');
            const messageEl = document.createElement('div');
            messageEl.className = isError ? 'error-box' : 'message-box';
            messageEl.innerHTML = `
                <strong>${title}</strong><br>
                ${message}<br>
                <small>⏰ ${timestamp}</small>
            `;
            
            // Remove "waiting" message if it exists
            const waitingMsg = messagesDiv.querySelector('em');
            if (waitingMsg) {
                waitingMsg.parentElement.remove();
            }
            
            messagesDiv.insertBefore(messageEl, messagesDiv.firstChild);
            
            // Keep only last 10 messages
            while (messagesDiv.children.length > 10) {
                messagesDiv.removeChild(messagesDiv.lastChild);
            }
        }

        function clearMessages() {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '<p><em>Waiting for real-time messages...</em></p>';
        }

        // Auto-generate test messages every 30 seconds (for demo)
        setInterval(() => {
            if (Math.random() > 0.7) { // 30% chance
                const demoMessages = [
                    'Auto demo: New auction started',
                    'Auto demo: Bid war in progress!',
                    'Auto demo: Popular item trending'
                ];
                const msg = demoMessages[Math.floor(Math.random() * demoMessages.length)];
                displayMessage('🤖 Auto Demo', msg, new Date().toLocaleTimeString());
            }
        }, 30000);
    </script>
</body>
</html>