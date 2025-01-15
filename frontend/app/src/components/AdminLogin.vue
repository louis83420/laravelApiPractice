<template>
    <div>
      <h2>後台管理員登入</h2>
      <form @submit.prevent="handleLogin">
        <label for="email">Email</label>
        <input v-model="email" type="email" required />
        <label for="password">Password</label>
        <input v-model="password" type="password" required />
        <button type="submit">登入</button>
      </form>
      <div v-if="error">{{ error }}</div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        email: '',
        password: '',
        error: null,
      };
    },
    methods: {
      async handleLogin() {
        try {
          const res = await axios.post('http://localhost/api/admin/login', {
            email: this.email,
            password: this.password,
          });
  
          // 儲存 Token 到 LocalStorage
          localStorage.setItem('access_token', res.data.access_token);
  
          // 導向管理員頁面
          this.$router.push('/admin');
        } catch (error) {
          this.error = '登入失敗，請檢查帳號或密碼';
        }
      },
    },
  };
  </script>
  