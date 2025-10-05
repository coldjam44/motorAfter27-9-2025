<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار التواصل المباشر - نظام المزادات</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            direction: rtl;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .status-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-right: 5px solid #28a745;
        }
        .control-panel {
            background: #e9ecef;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .message-display {
            background: #fff;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            max-height: 400px;
            overflow-y: auto;
        }
        .message-item {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
        }
        .auction-message {
            background: linear-gradient(45deg, #007bff, #6610f2);
        }
        .bid-message {
            background: linear-gradient(45deg, #fd7e14, #e83e8c);
        }
        button {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            margin: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }
        .btn-success { background: linear-gradient(45deg, #28a745, #20c997); }
        .btn-warning { background: linear-gradient(45deg, #ffc107, #fd7e14); }
        .btn-danger { background: linear-gradient(45deg, #dc3545, #c82333); }
        
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            margin: 8px 0;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚗 نظام التواصل المباشر للمزادات</h1>
            <p>تجربة تفاعلية للرسائل الفورية في نظام المزادات</p>
        </div>

        <div class="status-card">
            <h3>📡 حالة الاتصال</h3>
            <div id="connection-status">
                <span style="color: #28a745;">✅ متصل ومستعد للعمل</span>
            </div>
        </div>

        <div class="control-panel">
            <h3>🎯 إرسال رسائل تجريبية</h3>
            
            <div class="grid">
                <div>
                    <label>رسالة مخصصة:</label>
                    <input type="text" id="customMessage" placeholder="اكتب رسالتك هنا..." value="مزاد جديد على سيارة BMW 2023">
                </div>
                <div>
                    <label>مبلغ المزايدة:</label>
                    <input type="number" id="bidAmount" placeholder="المبلغ" value="15000">
                </div>
            </div>

            <div style="margin: 20px 0;">
                <button onclick="sendCustomMessage()">📤 إرسال رسالة مخصصة</button>
                <button onclick="sendBidUpdate()" class="btn-success">💰 محاكاة مزايدة جديدة</button>
                <button onclick="sendAuctionAlert()" class="btn-warning">⏰ تنبيه انتهاء المزاد</button>
                <button onclick="sendWinnerAlert()" class="btn-danger">🏆 إعلان الفائز</button>
            </div>
        </div>

        <div class="message-display">
            <h3>📨 الرسائل المباشرة</h3>
            <div id="messages">
                <div class="message-item">
                    <strong>🎉 مرحباً بك في نظام المزادات المباشر!</strong><br>
                    جميع الرسائل ستظهر هنا فورياً<br>
                    <small>⏰ ${new Date().toLocaleTimeString('ar-SA')}</small>
                </div>
            </div>
        </div>

        <div class="info-box">
            <h4>📋 كيفية الاستخدام:</h4>
            <ul>
                <li>انقر على الأزرار لإرسال رسائل تجريبية</li>
                <li>افتح هذه الصفحة في عدة تبويبات لرؤية التزامن</li>
                <li>يمكنك تخصيص الرسائل من الحقول أعلاه</li>
                <li>مناسب لمحاكاة مزادات السيارات الحقيقية</li>
            </ul>
        </div>

        <div class="control-panel">
            <h3>⚙️ إعدادات متقدمة</h3>
            <button onclick="startAutoDemo()">🤖 بدء العرض التلقائي</button>
            <button onclick="stopAutoDemo()">⏹️ إيقاف العرض</button>
            <button onclick="clearMessages()">🗑️ مسح الرسائل</button>
            <button onclick="exportMessages()">💾 تصدير الرسائل</button>
        </div>
    </div>

    <script>
        let autoDemo = null;
        let messageCounter = 0;

        // رسائل تجريبية للمزادات
        const auctionMessages = [
            { type: 'bid', text: 'مزايدة جديدة: {amount} ريال على سيارة تويوتا كامري 2022', icon: '💰' },
            { type: 'auction', text: 'مزاد جديد: مرسيدس بنز C300 موديل 2023', icon: '🚗' },
            { type: 'bid', text: 'العطاء الحالي: {amount} ريال - باقي دقيقتان!', icon: '⏰' },
            { type: 'winner', text: 'تهانينا! فزت بالمزاد بمبلغ {amount} ريال', icon: '🏆' },
            { type: 'auction', text: 'مشارك جديد انضم للمزاد', icon: '👤' },
            { type: 'bid', text: 'مزايدة مضادة: {amount} ريال', icon: '🔥' },
            { type: 'auction', text: 'النداء الأخير - المزاد ينتهي خلال 30 ثانية!', icon: '📢' }
        ];

        function sendCustomMessage() {
            const message = document.getElementById('customMessage').value;
            if (message.trim()) {
                displayMessage('📝 رسالة مخصصة', message, 'custom');
                triggerServerEvent(message);
            }
        }

        function sendBidUpdate() {
            const amount = document.getElementById('bidAmount').value || '15000';
            const message = `مزايدة جديدة: ${amount} ريال على سيارة BMW X5 2023`;
            displayMessage('💰 مزايدة جديدة', message, 'bid');
            triggerServerEvent(message);
        }

        function sendAuctionAlert() {
            const message = 'تنبيه: المزاد ينتهي خلال دقيقتين - ضع عطائك الأخير!';
            displayMessage('⏰ تنبيه المزاد', message, 'auction');
            triggerServerEvent(message);
        }

        function sendWinnerAlert() {
            const amount = document.getElementById('bidAmount').value || '25000';
            const message = `🎉 تهانينا! فزت بالمزاد بمبلغ ${amount} ريال`;
            displayMessage('🏆 إعلان الفائز', message, 'winner');
            triggerServerEvent(message);
        }

        function displayMessage(title, text, type = 'default') {
            const messagesDiv = document.getElementById('messages');
            const messageEl = document.createElement('div');
            
            let className = 'message-item';
            if (type === 'bid') className += ' bid-message';
            else if (type === 'auction') className += ' auction-message';
            
            messageEl.className = className;
            messageEl.innerHTML = `
                <strong>${title}</strong><br>
                ${text}<br>
                <small>⏰ ${new Date().toLocaleTimeString('ar-SA')}</small>
            `;
            
            messagesDiv.insertBefore(messageEl, messagesDiv.firstChild);
            
            // Keep only last 15 messages
            while (messagesDiv.children.length > 15) {
                messagesDiv.removeChild(messagesDiv.lastChild);
            }

            // Scroll to top to show new message
            messagesDiv.scrollTop = 0;
        }

        function triggerServerEvent(message) {
            // محاكاة إرسال للخادم
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
                console.log('Event sent to server:', data);
            })
            .catch(error => {
                console.log('Server event simulation:', error);
            });
        }

        function startAutoDemo() {
            if (autoDemo) return;
            
            displayMessage('🤖 العرض التلقائي', 'تم بدء العرض التلقائي - ستصل رسائل كل 5 ثوان', 'auction');
            
            autoDemo = setInterval(() => {
                const randomMsg = auctionMessages[Math.floor(Math.random() * auctionMessages.length)];
                const amount = Math.floor(Math.random() * 50000) + 10000;
                const text = randomMsg.text.replace('{amount}', amount.toLocaleString('ar-SA'));
                
                displayMessage(randomMsg.icon + ' تلقائي', text, randomMsg.type);
                triggerServerEvent(text);
            }, 5000);
        }

        function stopAutoDemo() {
            if (autoDemo) {
                clearInterval(autoDemo);
                autoDemo = null;
                displayMessage('⏹️ توقف العرض', 'تم إيقاف العرض التلقائي', 'auction');
            }
        }

        function clearMessages() {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = `
                <div class="message-item">
                    <strong>🧹 تم مسح الرسائل</strong><br>
                    جاهز لاستقبال رسائل جديدة<br>
                    <small>⏰ ${new Date().toLocaleTimeString('ar-SA')}</small>
                </div>
            `;
        }

        function exportMessages() {
            const messages = document.querySelectorAll('#messages .message-item');
            let exportText = 'تقرير رسائل المزاد\\n================\\n\\n';
            
            messages.forEach((msg, index) => {
                exportText += `${index + 1}. ${msg.textContent.trim()}\\n\\n`;
            });
            
            const blob = new Blob([exportText], { type: 'text/plain;charset=utf-8' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `auction_messages_${new Date().toISOString().split('T')[0]}.txt`;
            link.click();
            
            displayMessage('💾 تصدير', 'تم تصدير الرسائل بنجاح', 'auction');
        }

        // إرسال رسالة ترحيب عند تحميل الصفحة
        window.addEventListener('load', function() {
            setTimeout(() => {
                displayMessage('🎯 النظام جاهز', 'نظام المزادات المباشر يعمل بكفاءة - جرب الأزرار!', 'auction');
            }, 1000);
        });

        // محاكاة رسائل عشوائية كل 30 ثانية
        setInterval(() => {
            if (!autoDemo && Math.random() > 0.7) {
                const randomMsg = auctionMessages[Math.floor(Math.random() * auctionMessages.length)];
                const amount = Math.floor(Math.random() * 30000) + 5000;
                const text = randomMsg.text.replace('{amount}', amount.toLocaleString('ar-SA'));
                
                displayMessage(randomMsg.icon + ' تلقائي', text, randomMsg.type);
            }
        }, 30000);
    </script>
</body>
</html>