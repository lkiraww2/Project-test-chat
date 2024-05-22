<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .chat-container {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            display: flex;
            overflow: hidden;
            width: 90%;
            max-width: 900px;
        }
        .sidebar {
            flex: 1;
            background-color: #f0f0f0;
            overflow-y: auto;
            padding: 20px;
            border-right: 1px solid #ddd;
        }
        .sidebar h3 {
            margin-bottom: 10px;
        }
        .user-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .user-list button {
            margin-bottom: 10px;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            background-color: #e9ecef;
            transition: background-color 0.3s;
            width: 100%;
            border: none;
            text-align: left;
        }
        .user-list button:hover {
            background-color: #cfd8dc;
        }
        .chat-section {
            flex: 2;
            display: flex;
            flex-direction: column;
        }
        .messages {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
            border-bottom: 1px solid #ddd;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f1f1f1;
        }
        .message:nth-child(odd) {
            background-color: #e1f7d5;
        }
        .message-form {
            display: none;
            flex-direction: row;
            padding: 10px;
            background-color: #fafafa;
        }
        .message-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
        }
        .send-button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
        .send-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="chat-container">
        <div class="sidebar">
            <h3>User Accounts</h3>
            <ul class="user-list">
                @foreach($users as $u)
                    <button onclick="startChat({{ $u->id }})">
                        {{ $u->name }}
                    </button>
                @endforeach
            </ul>
        </div>
        <div class="chat-section">
            <div id="messages" class="messages">
                <!-- Messages will be loaded here -->
            </div>
            <form id="message-form" class="message-form" method="POST" action="{{ route('messages.store') }}">
                @csrf
                <select id="receiver-id" name="receiver_id" required hidden>
                    @foreach ($users as $user) 
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <input type="text" id="message-input" class="message-input" name="message" autocomplete="off" placeholder="Type your message here...">
                <button type="submit" class="send-button">Send</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.socket.io/4.1.3/socket.io.min.js"></script>
    <script>
        const socket = io('http://127.0.0.1:8000');
        function startChat(userId) {
            document.getElementById('receiver-id').value = userId;
            document.getElementById('message-form').style.display = 'flex';
            fetchMessages(userId);
            setInterval(() => fetchMessages(userId), 1000);
        }

        function fetchMessages(userId) {
            fetch(`/getMessages/${userId}`)
                .then(response => response.json())
                .then(messages => {
                    const messagesContainer = document.getElementById('messages');
                    messagesContainer.innerHTML = '';

                    messages.forEach(message => {
                        const messageElement = document.createElement('div');
                        messageElement.classList.add('message');
                        if (message.user_id === userId) {
                            messageElement.innerHTML = `<p>${message.message}</p>`;
                        } else {
                            messageElement.innerHTML = `<p><strong>You:</strong> ${message.message}</p>`;
                        }
                        messagesContainer.appendChild(messageElement);
                    });

                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                })
                .catch(error => console.error('Error fetching messages:', error));
        }

        const messageForm = document.getElementById('message-form');
        messageForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();
            const receiverId = document.getElementById('receiver-id').value;

            if (message === '') {
                alert('Please enter a message');
                return;
            }

            fetch(messageForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ receiver_id: receiverId, message: message })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Message sent successfully:', data);
                messageInput.value = data.message;
                const messagesContainer = document.getElementById('messages');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .catch(error => console.error('Error sending message:', error));
        });

        socket.on('connect', () => {
            console.log('Connected to Socket.IO server');
        });

        socket.on('chat', (data) => {
            console.log('Received new message:', data);
            const messageElement = document.createElement('div');
            messageElement.classList.add('message');
            messageElement.innerHTML = `<p>${data.senderName}: ${data.message}</p>`;
            document.getElementById('messages').appendChild(messageElement);

            const messagesContainer = document.getElementById('messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
    </script>
</body>
</html>
