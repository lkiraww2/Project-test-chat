<template>
    <div class="chat-section">
      <div ref="messagesContainer" class="messages">
        <div v-for="(message, index) in messages" :key="index" class="message">
          <p v-if="message.user_id === currentUserId">
            <strong>You:</strong> {{ message.message }}
          </p>
          <p v-else>
            {{ message.message }}
          </p>
        </div>
      </div>
      <MessageForm @send-message="sendMessage" />
    </div>
  </template>
  
  <script>
  import MessageForm from './MessageForm.vue';
  
  export default {
    props: {
      messages: {
        type: Array,
        default: () => []
      },
      currentUserId: {
        type: Number,
        default: null
      }
    },
    methods: {
      sendMessage(message) {
        // Send message logic
      }
    },
    components: {
      MessageForm
    }
  };
  </script>
  
  <style scoped>
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
  </style>
  