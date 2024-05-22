<template>
    <div class="sidebar">
      <h3>User Accounts</h3>
      <ul class="user-list">
        <li v-for="user in users" :key="user.id">
          <button @click="startChat(user.id)">
            {{ user.name }}
          </button>
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  export default {
    name: 'Sidebar',
    props: ['users'],
    methods: {
      startChat(userId) {
        this.$emit('start-chat', userId);
      }
    }
  };
  </script>
  
  <style scoped>
  .sidebar {
    flex: 1;
    background-color: #f0f0f0;
    overflow-y: auto;
    padding: 20px;
    border-right: 1px solid #ddd;
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
  </style>
  