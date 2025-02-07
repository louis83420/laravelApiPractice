<template>
  <div>
    <h2>消費者登入</h2>

    <!-- 傳統登入表單 -->
    <form @submit.prevent="handleLogin">
      <label for="email">Email</label>
      <input v-model="email" type="email" required />
      <label for="password">Password</label>
      <input v-model="password" type="password" required />
      <button type="submit">登入</button>
    </form>

    <!-- OAuth 2.0 登入按鈕 -->
    <div class="oauth-login">
      <button @click="redirectToOAuth" class="oauth-button">使用 OAuth 2.0 登入</button>
    </div>

    <!-- 登入錯誤訊息 -->
    <div v-if="error" class="error-message">{{ error }}</div>
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
    // 傳統登入方式
    async handleLogin() {
      try {
        const res = await axios.post('http://localhost/api/login', {
          email: this.email,
          password: this.password,
        });

        // 儲存 Access Token
        localStorage.setItem('token', res.data.access_token);
        this.$router.push('/');
      } catch (error) {
        this.error = '登入失敗，請檢查帳號或密碼';
      }
    },

    // OAuth 2.0 登入流程：直接導向平台登入頁面
    redirectToOAuth() {
      // 直接將使用者導向你的平台登入頁面
      window.location.href = 'http://localhost:3000/login';
    }
  },
};
</script>

<style scoped>
h2 {
  font-size: 24px;
  margin-bottom: 20px;
}
.oauth-login {
  margin-top: 20px;
}
.oauth-button {
  background-color: #4285F4;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}
.oauth-button:hover {
  background-color: #357ae8;
}
.error-message {
  color: red;
  margin-top: 10px;
}
</style>
