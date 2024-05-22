<template>
    <div class="chat-container">
      <Sidebar :users="users" @start-chat="startChat" />
      <ChatSection :messages="messages" :currentUserId="currentUserId" @send-message="sendMessage" />
    </div>
  </template>
  
  <script>
  import Vue from 'vue';
  import io from 'socket.io-client';
  import Sidebar from './components/Sidebar.vue';
  import ChatSection from './components/ChatSection.vue';
  
  export default {
    name: 'App',
    components: {
      Sidebar,
      ChatSection
    },
    data() {
      return {
        users: [],
        messages: [],
        currentUserId: null,
        socket: null
      };
    },
    created() {
      this.socket = io('http://127.0.0.1:8000');
  
      // Socket event listeners
      this.socket.on('connect', () => {
        console.log('Connected to Socket.IO server');
      });
  
      this.socket.on('chat', (data) => {
        this.messages.push(data);
        this.scrollToBottom();
      });
    },
    methods: {
      startChat(userId) {
        this.currentUserId = userId;
        this.fetchMessages(userId);
      },
      fetchMessages(userId) {
        fetch(`/getMessages/${userId}`)
          .then(response => response.json())
          .then(messages => {
            this.messages = messages;
            this.scrollToBottom();
          })
          .catch(error => console.error('Error fetching messages:', error));
      },
      sendMessage(message) {
        fetch('/messages', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          },
          body: JSON.stringify(message)
        })
        .then(response => response.json())
        .then(data => {
          console.log('Message sent successfully:', data);
          this.messages.push(data);
          this.scrollToBottom();
          this.socket.emit('chat', data); // Emit message to Socket.IO server
        })
        .catch(error => console.error('Error sending message:', error));
      },
      scrollToBottom() {
        Vue.nextTick(() => {
          const messagesContainer = this.$refs.messagesContainer;
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
      },
      getCsrfToken() {
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenElement) {
          console.error('CSRF token meta tag not found');
          return '';
        }
        return csrfTokenElement.getAttribute('content');
      }
    }
  };
  </script>
  
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
  </style>
  