<template>
    <div>
      <h1>API 測試</h1>
      <button @click="callApi">呼叫 API</button>
      <p v-if="message">{{ message }}</p>
    </div>
  </template>
  
  <script>
  import apiClient from '../axios';
  
  export default {
    data() {
      return {
        message: ''
      };
    },
    methods: {
      async callApi() {
        try {
          const response = await apiClient.get('/products');
          this.message = response.data.message;
        } catch (error) {
          console.error('API 呼叫失敗:', error);
        }
      }
    }
  };
  </script>
  
  <style>
  /* 您的樣式 */
  </style>
  