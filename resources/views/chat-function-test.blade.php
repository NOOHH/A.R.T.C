<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Function Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .message {
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 12px;
            max-width: 80%;
            word-wrap: break-word;
        }
        
        .message.user {
            background-color: #007bff;
            color: white;
            margin-left: auto;
            text-align: right;
        }
        
        .message.bot,
        .message.system {
            background-color: #f8f9fa;
            color: #333;
            margin-right: auto;
            text-align: left;
        }
        
        .message-content {
            display: flex;
            flex-direction: column;
        }
        
        .message-sender {
            font-weight: bold;
            font-size: 0.9em;
            margin-bottom: 4px;
            color: #666;
        }
        
        .message-text {
            margin-bottom: 4px;
            line-height: 1.4;
        }
        
        .message-time {
            font-size: 0.8em;
            opacity: 0.7;
        }
        
        .user-message .message-sender {
            color: #cce7ff;
        }
        
        .user-message .message-time {
            color: #cce7ff;
        }
        
        #chatMessages {
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Chat Function Test</h3>
                    </div>
                    <div class="card-body">
                        <div id="chatMessages"></div>
                        
                        <div class="mt-3">
                            <form id="testChatForm" class="d-flex gap-2">
                                <input type="text" id="testChatInput" class="form-control" placeholder="Test message...">
                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>
                        
                        <div class="mt-3">
                            <h5>Test Functions:</h5>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary" onclick="testUserMessage()">User Message</button>
                                <button class="btn btn-outline-secondary" onclick="testBotMessage()">Bot Message</button>
                                <button class="btn btn-outline-success" onclick="testSystemMessage()">System Message</button>
                                <button class="btn btn-outline-danger" onclick="clearMessages()">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add message to chat
        function addMessage(message, type, sender = null) {
            const messagesContainer = document.getElementById('chatMessages');
            if (!messagesContainer) return;

            const messageElement = document.createElement('div');
            messageElement.className = `message ${type}`;
            
            const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            let messageHTML = '';
            
            if (type === 'user') {
                messageHTML = `
                    <div class="message-content user-message">
                        <div class="message-text">${message}</div>
                        <div class="message-time">${timestamp}</div>
                    </div>
                `;
            } else if (type === 'bot' || type === 'system') {
                const senderName = sender || 'System';
                messageHTML = `
                    <div class="message-content bot-message">
                        <div class="message-sender">${senderName}</div>
                        <div class="message-text">${message}</div>
                        <div class="message-time">${timestamp}</div>
                    </div>
                `;
            } else {
                // Other message types (like received messages)
                const senderName = sender || 'Unknown';
                messageHTML = `
                    <div class="message-content other-message">
                        <div class="message-sender">${senderName}</div>
                        <div class="message-text">${message}</div>
                        <div class="message-time">${timestamp}</div>
                    </div>
                `;
            }
            
            messageElement.innerHTML = messageHTML;
            messagesContainer.appendChild(messageElement);
            
            // Auto-scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Test functions
        function testUserMessage() {
            addMessage('This is a test user message', 'user');
        }
        
        function testBotMessage() {
            addMessage('This is a test bot response', 'bot', 'Test Bot');
        }
        
        function testSystemMessage() {
            addMessage('This is a system message', 'system', 'System');
        }
        
        function clearMessages() {
            document.getElementById('chatMessages').innerHTML = '';
        }
        
        // Form submission
        document.getElementById('testChatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('testChatInput');
            const message = input.value.trim();
            
            if (message) {
                addMessage(message, 'user');
                input.value = '';
                
                // Simulate bot response
                setTimeout(() => {
                    addMessage('Echo: ' + message, 'bot', 'Echo Bot');
                }, 500);
            }
        });
        
        // Add initial messages
        addMessage('Welcome to the chat function test!', 'system', 'System');
        addMessage('Type a message or use the test buttons above', 'system', 'System');
    </script>
</body>
</html>
