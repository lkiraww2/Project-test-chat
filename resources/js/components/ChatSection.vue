<template>
    <div class="chat-section">
      <div ref="messagesContainer" class="messages">
        <div v-for="(message, index) in messages" :key="index" class="message" :class="{'message-self': message.user_id === currentUserId}">
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
    name: 'ChatSection',
    components: {
      MessageForm
    },
    props: ['messages', 'currentUserId'],
    methods: {
      sendMessage(message) {
        this.$emit('send-message', message);
      }
    },
    mounted() {
      this.scrollToBottom();
    },
    updated() {
      this.scrollToBottom();
    },
    watch: {
      messages() {
        this.scrollToBottom();
      }
    },
    updated() {
      this.scrollToBottom();
    },
    methods: {
      scrollToBottom() {
        this.$nextTick(() => {
          const messagesContainer = this.$refs.messagesContainer;
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
      }
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
  .message-self {
    background-color: #e1f7d5;
  }
  .message:nth-child(odd) {
    background-color: #e1f7d5;
  }
  .message-form {
    display: flex;
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
  